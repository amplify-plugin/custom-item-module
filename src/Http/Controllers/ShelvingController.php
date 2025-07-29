<?php

namespace Amplify\System\CustomItem\Http\Controllers;

use Amplify\System\CustomItem\Http\Requests\AddToOrderRequest;
use Amplify\System\CustomItem\Partials\ShelvingProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShelvingController extends BaseController
{
    /**
     * Get And Calculate Shelving Product Price
     *
     * @return [type]
     */
    public function shelvingProductPrice(Request $request)
    {
        $product = (new ShelvingProduct($request))->getPrice();

        return response()->json($product);
    }

    /**
     * Shelving Add to cart functionality
     *
     * @param  Request  $request
     * @return [response]
     */
    public function addToCart(AddToOrderRequest $request)
    {

        DB::beginTransaction();
        try {
            $cart = getOrCreateCart();
            $dbProduct = Product::with('productImage')->whereProductCode($request->input('product'))->first();
            $product_code = $request->input('product');
            $warehouse = $this->getContactWarehouse();
            $additionalInfo = json_encode([
                'depth' => $request->depth,
                'width' => $request->width,
                'qty' => $request->qty,
                'depthDisplay' => $request->depthDisplay,
                'widthDisplay' => $request->widthDisplay,
                'diameter' => $request->diameter,
                'finish' => $request->finish,
                'product' => $request->product,
                'OrderSpec' => $request->OrderSpec,
                // common value
                'uom_qty' => $request->qty,
                'price' => $request->price,
                'total_price' => $request->totalPrice,
                'uom' => 'SI',
            ]);
            $depth = $request->depth;
            $width = $request->width;
            $cart_item_identifier = [
                'product_id' => $dbProduct->id,
                'product_code' => $product_code,
                'source_type' => 'custom_item',
                'additional_info' => $additionalInfo,
                'product_warehouse_code' => $warehouse ?? '',
                'warehouse_id' => $warehouse ?? '',
                'quantity' => number_format($depth) * number_format($width) * number_format($request->qty),
                'unitprice' => $request->input('price'),
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
