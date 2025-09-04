<?php

namespace Amplify\System\CustomItem\Http\Controllers;

use Amplify\System\CustomItem\Traits\CustomItemERPService;
use App\Models\CustomProduct;
use App\Models\Product;
use DB;
use Illuminate\Http\Request;

class DrainTubeHeaterController extends BaseController
{
    use CustomItemERPService;

    /**
     * Get Heater Wire Product By Code
     *
     * @return [type]
     */
    public function getProductByCode()
    {
        $drainTubeHeater = CustomProduct::whereType('drain-tube-heater')->firstOrFail();
        $code = json_decode($drainTubeHeater->value)?->product;
        $product = $this->getProductDetailsFromERP(code: $code);

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
            $additionalInfo = json_encode([
                'voltage' => $request->voltage,
                'qty' => $request->quantity,
                'product_code' => $request->product,
                'uom_qty' => $request->quantity,
                'length' => $request->length,
                'total_price' => $request->total_price,
                'OrderSpec' => $request->order_spec,
                'uom' => 'FT',
            ]);

            $cart_item_identifier = [
                'product_id' => $dbProduct->id,
                'product_code' => $product_code,
                'source_type' => 'custom_item',
                'additional_info' => $additionalInfo,
                'quantity' => $request->input('total_square_feet'),
                'unitprice' => $request->input('price'),
                'product_warehouse_code' => $warehouse ?? '',
                'warehouse_id' => $warehouse ?? '',
                'address_id' => customer_check() ? customer(true)->customer_address_id : null,
                'product_name' => $dbProduct->product_name,
                'product_image' => $dbProduct->thumbnail,
            ];
            $cart->cartItems()->create($cart_item_identifier);
            DB::commit();

            return response()->json([
                'cart_summary' => route('frontend.carts.index'),
                'shop' => frontendShopURL(),
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
