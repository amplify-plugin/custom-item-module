<?php

namespace Amplify\System\CustomItem\Http\Controllers;

use Amplify\System\CustomItem\Http\Controllers\AddToOrderRequest;
use Amplify\System\CustomItem\Traits\CustomItemERPService;
use App\Models\Cart;
use App\Models\CustomProduct;
use App\Models\Product;
use DB;
use Illuminate\Http\Request;

class HeaterWireController extends BaseController
{
    use CustomItemERPService;

    public function getProducts()
    {
        $heaterWireCiruts = CustomProduct::whereType('heater-wire-circuits')->firstOrFail();

        return json_decode($heaterWireCiruts->value)?->products;
    }

    /**
     * Get Heater Wire Product By Code
     *
     * @return [type]
     */
    public function getProductByCode(Request $request)
    {
        $code = $request->input('code');
        $product = $this->getProductDetailsFromERP($code);

        return response()->json($product);
    }

    /**
     * Heater Wire Add to cart
     *
     * @param  AddToOrderRequest  $request
     * @return [type]
     */
    public function addToCart(Request $request)
    {
        DB::beginTransaction();
        try {
            $cart = getOrCreateCart();
            $dbProduct = Product::with('productImage')->whereProductCode($request->input('product'))->first();
            $warehouse = $this->getContactWarehouse();
            $product_code = $request->input('product');
            $warehouse = $this->getContactWarehouse();
            $additionalInfo = json_encode([
                'wraps' => $request->wraps,
                'voltage' => $request->voltage,
                'qty' => $request->totalFeet,
                'product_code' => $request->product,
                'totalFeet' => $request->totalFeet,
                'temp' => $request->temp,
                'inches' => $request->inches,

                // common value
                'uom_qty' => $request->qty,
                'price' => $request->price,
                'total_price' => $request->totalPrice,
                'OrderSpec' => $request->OrderSpec,
                'uom' => 'FT',
            ]);

            $cart_item_identifier = [
                'product_id' => $dbProduct->id,
                'product_code' => $product_code,
                'source_type' => 'custom_item',
                'additional_info' => $additionalInfo,
                'quantity' => $request->input('totalFeet'),
                'unitprice' => $request->input('price'),
                'product_warehouse_code' => $warehouse ?? '',
                'warehouse_id' => $warehouse ?? '',
                'address_id' => customer_check() ? customer(true)->customer_address_id : null,
                'product_name' => $dbProduct->product_name,
                'product_image' => $dbProduct->productImage->main ?? null,
            ];
            $cart->cartItems()->create($cart_item_identifier);
            DB::commit();

            return response()->json([
                'cart_summary' => route('frontend.carts.index'),
                'shop' => route('frontend.shop.index'),
                'message' => 'Success!',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }
}
