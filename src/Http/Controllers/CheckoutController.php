<?php

namespace Amplify\System\CustomItem\Http\Controllers;

use Amplify\ErpApi\Facades\ErpApi;
use Amplify\System\OrderRule\Facades\OrderRuleCheck;
use App\Models\Cart;
use App\Models\CustomerOrder;
use App\Models\CustomerOrderLine;
use App\Models\SystemConfiguration;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class CheckoutController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse [type]
     */
    public function getShippingOption(Request $request): JsonResponse
    {
        $cart = getCart();

        $cart->load('cartItems');

        $inputs = $request->all();

        $inputs['items'] = $cart->cartItems->toArray();

        $shippingMethods = ErpApi::getShippingOption($inputs);

        $clientCode = config('amplify.basic.client_code');

        if ($clientCode === 'ACT' && is_array($shippingMethods) && ! empty($shippingMethods['FreightRate']['WILL CALL'])) {
            $shippingMethods = $this->getWillCallMethods([], $shippingMethods);
        }

        return response()->json($shippingMethods);
    }


    /**
     * This function submits the order & quotation
     */
    public function submitOrder(Request $request): JsonResponse
    {
        $cart = getCart();

        $customerDetails = ErpApi::getCustomerDetail();
        $order_data = $request->all();
        $order_quote = $this->getOrderPricing($request);
        try {
            $order_lines = [];
            foreach ($cart->cartItems as $product) {
                $order_lines[] = new CustomerOrderLine([
                    'product_id' => $product->product_id,
                    'product_code' => $product->product_code,
                    'warehouse_id' => $product->warehouse_id ?? null,
                    'qty' => (int) $product->quantity,
                    'customer_price' => (float) $product->unitprice,
                    'ship_to_address_id' => $product->address_id,
                    'source_type' => $product->source_type ?? null,
                    'source' => $product->source ?? null,
                    'additional_info' => $product->additional_info ?? null,
                    'unit_code' => $product->uom ?? null,
                ]);
            }

            $order = new CustomerOrder;
            $order->total_net_price = $order_quote['order_subtotal'] ?? 0;
            $order->total_tax_amount = $order_quote['order_tax'] ?? 0;
            $order->total_amount = $order_quote['order_total'] ?? 0;
            $order->order_type = ($order_data['order_type'] == 'draft' || $order_data['order_type'] == 'order') ? CustomerOrder::IS_ORDER_TYPE : CustomerOrder::IS_RFQ_TYPE;
            $order->web_order_number = $this->getFormattedWebOrderNumber();
            $order->customer_id = customer()?->id;
            $order->contact_id = customer(true)?->id;
            $order->draft_name = $request->draft_name ?? null;
            $order->customer_order_number = $order_data['po_number'] ?? null;

            $order->customer_name = $order_data['customer_name'];
            $order->email = $order_data['customer_email'];
            $order->phone = $order_data['shipping_phone'] ?? $order_data['customer_phone'];
            $addressParts = array_filter([
                $order_data['address_1'] ?? '',
                $order_data['address_2'] ?? '',
                $order_data['address_3'] ?? '',
            ]);
            $order->ship_to_address = implode(', ', $addressParts);

            $order->ship_to_city = $order_data['address_city'];
            $order->ship_to_country_code = $order_data['address_country_code'];
            $order->ship_to_state = $order_data['address_state'];
            $order->ship_to_zip_code = $order_data['address_zip_code'];
            $order->spare_1 = array_key_exists('hazmat_charge', $order_data)
                ? json_encode(['hazmat_charge' => $order_data['hazmat_charge']])
                : null;

            // ===========================================
            // $order->user_id = customer(true)->id;
            $order->total_shipping_cost = $request->shipping_amount;
            $order->shipping_method = $request->shipping_method;
            $order->shipping_number = $request->shipping_number;
            // ===========================================

            if ($customerDetails->CreditCardOnly == 'Y') {
                $order->order_status = 'Payment Pending';
            } elseif ($this->checkCustomerOrderLimit($order->total_amount)) {
                $order->order_status = 'Approved';
            } else {
                $order->order_status = 'Pending';
            }

            if ($order->save() && $order->orderLines()->saveMany($order_lines)) {
                if ($request->filled('order_notes')) {
                    $order->orderNotes()->create([
                        'note' => $request->input('order_notes'),
                    ]);
                }
                if ($request->filled('internal_notes')) {
                    $order->orderNotes()->create([
                        'note' => $request->input('internal_notes'),
                        'subject' => 'Internal Note',
                        'type' => 'INT',
                    ]);
                }
                $jsonResponse['success'] = true;
                $nxt_available_web_order_number = (int) config('amplify.basic.nxt_available_web_order_number');
                SystemConfiguration::setValue('basic', 'nxt_available_web_order_number', str_pad(
                    ++$nxt_available_web_order_number, 7, '0', STR_PAD_LEFT
                ));

                // create an order with standard payment type
                switch ($order_data['order_type']) {
                    case 'order' :
                        if (customer_check() && config('amplify.order.order_rule_check', false)) {
                            $order->approval_status = OrderRuleCheck::check($order);
                            if ($order->approval_status == 'need_approver') {
                                $order->order_status = 'Awaiting Approval';
                                $order->save();

                                $this->customerCartUpdate($cart);

                                $jsonResponse['redirect_to'] = route(('frontend.order-awaiting-approvals.index'));
                                $jsonResponse['message'] = 'Your Order Is Waiting For Approval';
                                $jsonResponse['success'] = true;

                                return response()->json($jsonResponse, 200);
                            }
                        }

                        $erp_order_data = $order_data;
                        $erp_order_data['order_type'] = 'O';

                        if (config('amplify.basic.client_code') === 'STV') {
                            $wtdoNote = $this->generateWTDONote($cart->cartItems, $customerDetails);
                            if ($wtdoNote) {
                                $erp_order_data['wtdo_note'] = $wtdoNote;
                            }
                        }
                        $apiResponse = $order->createOrderOrQuoteERP($erp_order_data);
                        if (! $apiResponse['success']) {
                            throw new \ErrorException('Order submission failed');
                        }
                        $jsonResponse['redirect_to'] = $request->boolean('redirect_to_order_complete', false) ? URL::signedRoute('frontend.orders.completed', $order->id) : route('frontend.orders.index');
                        $jsonResponse['message'] = 'Order submitted successfully';
                        if (! empty($apiResponse['order_id'])) {
                            $jsonResponse['order_number'] = $apiResponse['order_id'];
                        }
                        break;
                    default:
                        $jsonResponse['message'] = 'Something went wrong.';
                        $jsonResponse['redirect_to'] = null;
                        break;
                }
                $this->customerCartUpdate($cart);
            } else {
                throw new \PDOException(($order_data['order_type'] === 'order') ? 'Order submission failed' : 'Quotation submission failed');
            }

            $cart->update(['status' => false]);

            return response()->json($jsonResponse, 200);

        } catch (\Exception $exception) {
            $class = basename(get_class($exception));

            Log::error("Create Order {$class} : ".$exception->getMessage().' in '.$exception->getFile().':'.$exception->getLine());

            $jsonResponse['success'] = false;
            $jsonResponse['message'] = $exception->getMessage();
            $jsonResponse['redirect_to'] = null;

            return response()->json($jsonResponse, 500);
        }
    }

    private function getOrderPricing(Request $request)
    {
        $totalOrderValue = (float) $request->input('total_order_value', 0);
        $salesTaxAmount = (float) $request->input('sales_tax_amount', 0);
        $freightAmount = (float) $request->input('freight_amount', 0);
        $hazmatCharge = (float) $request->input('hazmat_charge', 0);

        $totalAmount = $totalOrderValue + $salesTaxAmount + $freightAmount + $hazmatCharge;

        if(config('amplify.basic.client_code') === 'STV') {
            $totalAmount = $totalOrderValue + $freightAmount;
            $totalOrderValue = (float) $request->input('sub_total', 0);
        }

        return [
            'order_subtotal' => $totalOrderValue,
            'order_tax' => $salesTaxAmount,
            'order_ship' => $freightAmount,
            'order_total' => $totalAmount,
            'threshold_limit' => config('amplify.marketing.free_ship_threshold'),
            'threshold_message' => config('amplify.marketing.checkout_threshold_replace'),

        ];
    }

    private function getFormattedWebOrderNumber()
    {
        $web_order_prefix = config('amplify.basic.web_order_prefix');

        if (config('amplify.basic.nxt_available_web_order_number') == null) {
            SystemConfiguration::setValue('basic', 'basic.nxt_available_web_order_number', '0000001');
        }

        // get the prefix of the customer set in the admin panel then concat with it the available web_order_number
        return $web_order_prefix.config('amplify.basic.nxt_available_web_order_number');
    }

    /**
     * This checks customer order limit daily, monthly and for a single order
     */
    private function checkCustomerOrderLimit($order_amount): bool
    {
        $contact = optional(customer(true));
        $customer_orders = CustomerOrder::where('contact_id', $contact->id)->where('order_type', '0');

        $this_months_purchase = $customer_orders->whereMonth('created_at', Carbon::today()->month)->sum('total_amount'); // get the sum of all orders of the current month
        $todays_purchase = $customer_orders->whereDate('created_at', Carbon::today())->sum('total_amount'); // get the sum of the orders made today

        return $order_amount < $contact->order_limit
            && $contact->monthly_budget_limit > $this_months_purchase + $order_amount
            && $contact->daily_budget_limit > $todays_purchase + $order_amount;
    }

    /**
     * customerCartUpdate
     *
     * @param  mixed  $cart
     */
    public function customerCartUpdate(Cart $cart): void
    {
        $cart->update([
            'status' => 0,
            'tax',
        ]);
    }

    /**
     * @return array|mixed
     */
    public function getWillCallMethods(mixed $carts, mixed $shippingMethods): mixed
    {
        $products = ErpApi::getProductPriceAvailability([
            'items' => $carts->map(fn ($item): array => [
                'item' => $item['ItemNumber'],
                'qty' => $item['OrderQty'],
            ]),
            'warehouse' => $carts->pluck('WarehouseID')->unique()->implode(','),
        ]);

        $warehouses = Warehouse::whereIn('code', $carts->pluck('WarehouseID')->unique()->toArray())->wherePickupLocation(true)
            ->get()
            ->groupBy('name')
            ->mapWithKeys(function ($items, $key): array {
                $item = $items->first();

                return [
                    strtoupper($key) => [
                        'shipvia' => 'WILL CALL',
                        'fullday' => '',
                        'date' => '',
                        'nrates' => '',
                        'amount' => '0.00',
                        'address1' => $item['address'],
                        'address2' => '',
                        'city' => '',
                        'state' => '',
                        'zip' => $item['zip_code'],
                        'email' => $item['email'],
                        'telephone' => $item['telephone'],
                    ],
                ];
            })
            ->toArray();

        $quantityAvailable = $products->pluck('QuantityAvailable')->toArray();

        $isBackOrder = $carts
            ->zip($quantityAvailable)
            ->contains(function ($pair): bool {
                [$cart, $quantity] = $pair;

                return $quantity < $cart['OrderQty'];
            });

        if (count($warehouses) > 0 && ! $isBackOrder) {
            $shippingMethods['FreightRate']['WILL CALL'][0] = $warehouses;
        } else {
            unset($shippingMethods['FreightRate']['WILL CALL']);
        }

        return $shippingMethods;
    }

   private function generateWTDONote($cartItems, $customerDetails): ?string
    {
        $defaultWarehouse = $customerDetails->DefaultWarehouse ?? 'MAIN';
        $publicWarehouses = ['COR1', 'ORE1', 'CEL1', 'MAIN'];

        $items = [];
        foreach ($cartItems as $product) {
            $items[] = [
                'item' => $product->product_code,
                'qty' => (int) $product->quantity,
            ];
        }

        $filters = [
            'items' => $items,
            'warehouse' => implode(',', $publicWarehouses),
        ];

        $availabilities = ErpApi::getProductPriceAvailability($filters);

        $wtdoNotes = [];

        foreach ($items as $item) {
            $prodCode = $item['item'];
            $orderedQty = $item['qty'];

            $defaultQty = 0;
            $otherStocks = [];

            foreach ($availabilities as $availability) {
                if ($availability->ItemNumber != $prodCode) continue;

                $whse = $availability->WarehouseID;
                $qtyAvail = (int) $availability->QuantityAvailable;

                if ($whse === $defaultWarehouse) {
                    $defaultQty = $qtyAvail;
                } elseif (in_array($whse, $publicWarehouses)) {
                    if ($qtyAvail > 0) {
                        $otherStocks[] = "{$whse} Qty Avail: {$qtyAvail} for Part: {$prodCode}";
                    }
                }
            }

            if ($defaultQty < $orderedQty && count($otherStocks)) {
                $note = implode("\n", $otherStocks);
                $note .= "\n{$defaultWarehouse} Qty Avail: {$defaultQty} for Part: {$prodCode}";
                $wtdoNotes[] = $note;
            }
        }

        return count($wtdoNotes) ? implode("\n\n", $wtdoNotes) : null;
    }

}
