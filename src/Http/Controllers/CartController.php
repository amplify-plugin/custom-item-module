<?php

namespace Amplify\System\CustomItem\Http\Controllers;

use Amplify\ErpApi\Facades\ErpApi;
use Amplify\System\Backend\Http\Requests\Orders\QuickOrderAddToOrderRequest;
use Amplify\System\Factories\NotificationFactory;
use App\Models\Event;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends BaseController
{
    public function __construct()
    {
        if (! config('amplify.frontend.guest_checkout')) {
            $this->middleware('customers');
        }
    }

    public function addToCart(QuickOrderAddToOrderRequest $request)
    {
        if (customer_check()) {
            abort_if(! customer(true)->can('order.create'), 403, 'You don\'t have permission to add to cart.');
        }

        if (! ErpApi::enabled()) {
            return response()->json(['success' => false, 'message' => 'ERP service not enabled.']);
        }

        DB::beginTransaction();

        try {
            $is_added_to_cart = false;
            $erpProductDetails = $this->getERPInfo($request->products);
            $warehouses = ErpApi::getWarehouses();
            $cart = getOrCreateCart();

            foreach ($request->products as $product) {
                $source_info = [];
                $product_code = $product['product_code'];
                $dbProduct = Product::with('productImage')->whereProductCode($product_code)->first();
                if ($dbProduct->single_product_page_id) {
                    return response()->json([
                        'redirect' => true,
                        'route' => frontendSingleProductURL($dbProduct),
                    ], 200);
                }
                $product_warehouse_code = $product['product_warehouse_code'] ?? $this->getContactWarehouse();
                $warehouse = $warehouses->firstWhere('WarehouseNumber', '=', $product_warehouse_code);
                $warehouse_id = ($warehouse) ? $warehouse->InternalId : null;
                $warehouse_code = ($warehouse) ? $warehouse->WarehouseNumber : null;
                $product_qty = $product['qty'];
                $erpProduct = $erpProductDetails->first(fn ($item) => trim($item->ItemNumber) == $product_code);
                $product_price = $this->generateProductPrice($product, $erpProduct);

                $cart_item_identifier = [
                    'product_id' => $dbProduct->id,
                    'product_code' => $product_code,
                ];

                if (isset($product['source_type'])) {
                    $source_info = [
                        'source_type' => $product['source_type'],
                        'source' => $product['source'],
                        'expiry_date' => $product['expiry_date'],
                        'additional_info' => $product['additional_info'],
                    ];
                }

                $cart->cartItems()->where($cart_item_identifier)->delete();
                $cart->cartItems()->create($cart_item_identifier + $source_info + [
                    'quantity' => $product_qty,
                    'unitprice' => $product_price,
                    'product_warehouse_code' => $warehouse_code,
                    'warehouse_id' => $warehouse_id,
                    'address_id' => customer_check() ? customer(true)->customer_address_id : null,
                    'product_name' => $dbProduct->product_name,
                    'product_image' => $dbProduct->productImage->main ?? null,
                ]);

                if (! $is_added_to_cart) {
                    $is_added_to_cart = true;
                }
            }

            DB::commit();

            return response()->json([
                'success' => $is_added_to_cart,
                'message' => $is_added_to_cart ? 'Added to the order successfully.' : 'Product not available, Try again later.',
            ], $is_added_to_cart ? 200 : 500);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function createOrderFromQuote(Request $request)
    {
        try {
            NotificationFactory::callIf(
                true,
                Event::CREATE_ORDER_FROM_QUOTATION,
                [
                    'quote_id' => $request->quote_id,
                    'customer_id' => customer(true)->id,
                    'additional_info' => $request->additional_info,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'E-mail sent to sales person to create order',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public static function getERPInfo(array|string $codes, int $quantity = 1, $warehouse = null)
    {
        $itemWarehouse = ErpApi::getCustomerDetail()?->DefaultWarehouse ?? null;

        if (is_array($codes)) {
            $items = array_map(function ($item) use (&$itemWarehouse) {
                if (isset($item['product_warehouse_code']) && ! empty($item['product_warehouse_code'])) {
                    $itemWarehouse = $item['product_warehouse_code'];
                }

                return [
                    'item' => $item['product_code'],
                    'qty' => $item['qty'],
                ];
            }, $codes);

        } else {
            $items = [[
                'item' => $codes,
                'qty' => $quantity,
            ]];

            if ($warehouse) {
                $itemWarehouse = $warehouse;
            }
        }

        return ErpApi::getProductPriceAvailability([
            'items' => $items,
            'warehouse' => $itemWarehouse,
        ]);
    }

    public function generateProductPrice($cart_item, $erpProduct)
    {
        $product_price = has_erp_customer() ? $erpProduct->Price : ($erpProduct->ListPrice ?? $erpProduct->Price);

        switch ($cart_item['source_type'] ?? null) {
            case 'CAMPAIGN':
                $campaign = ErpApi::getCampaignDetail(['promo' => $cart_item['source'], 'override_date' => '10/23/2017']);
                $campaignItem = $campaign->CampaignDetail?->where('Item', trim($cart_item['product_code']))->firstOrFail();
                $product_price = $campaignItem?->Price ?? 0.00;

                break;

            default:
                for ($i = 1; $i <= 6; $i++) {
                    if (isset($erpProduct["QtyBreak_{$i}"]) && $erpProduct["QtyBreak_{$i}"] <= $cart_item['qty']) {
                        $product_price = $erpProduct["QtyPrice_{$i}"];

                        continue;
                    }
                    break;
                }

                break;
        }

        return $product_price;
    }
}
