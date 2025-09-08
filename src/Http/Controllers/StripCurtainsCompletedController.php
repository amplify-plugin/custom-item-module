<?php

namespace Amplify\System\CustomItem\Http\Controllers;

use Amplify\System\Backend\Models\Product;
use Amplify\System\CustomItem\Traits\CustomItemERPService;
use DB;
use Illuminate\Http\Request;

class StripCurtainsCompletedController extends BaseController
{
    use CustomItemERPService;

    /**
     * Get Strip replacement Product Price
     *
     * @param  mixed  $code
     * @return [type]
     */
    public function getStripProductPrice($code)
    {
        $product = $this->getProductDetailsFromERP(code: $code);

        return response()->json($product);
    }

    /**
     * StripCurtains Replacement add to cart
     *
     * @return [type]
     */
    public function addToCart(Request $request)
    {
        DB::beginTransaction();
        try {
            $cart = getOrCreateCart();
            $dbProduct = Product::with('productImage')->whereProductCode($request->input('product'))->first();
            $product_code = $request->input('product');
            $warehouse = $this->getContactWarehouse();
            $additionalInfo = json_encode([
                'product' => $request->product,
                'qty' => $request->qty,
                // common value
                'uom_qty' => $request->qty,
                'price' => $request->price,
                'total_price' => $request->totalPrice,
                'OrderSpec' => $request->OrderSpec,
                'uom' => 'SI',
            ]);
            $depth = $request->depth;
            $height = $request->height;
            $cart_item_identifier = [
                'product_id' => $dbProduct->id,
                'product_code' => $product_code,
                'source_type' => 'custom_item',
                'additional_info' => $additionalInfo,
                'quantity' => number_format($depth) * number_format($height) * number_format($request->qty),
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
