<?php

namespace Amplify\System\CustomItem\Http\Controllers;

use Amplify\System\CustomItem\Traits\CustomItemERPService;
use Amplify\Frontend\Traits\HasDynamicPage;
use App\Models\Cart;
use App\Models\CustomProduct;
use App\Models\Product;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StripCurtainsController extends BaseController
{
    use CustomItemERPService, HasDynamicPage;

    /**
     * Get Strips Replacement Product From JSON File
     *
     * @return \Illuminate\Http\JsonResponse [type]
     */
    public function getStrips(): JsonResponse
    {
        $stripReplacementData = CustomProduct::whereType('replacement-strip-curtains')->firstOrFail();

        return response()->json([$stripReplacementData->value]);
    }

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
                'OrderSpec' => $request->OrderSpec,
                // common value
                'uom_qty' => $request->qty,
                'price' => $request->price,
                'total_price' => $request->totalPrice,
                'uom' => 'FT',
            ]);
            $qty = $request->input('qty');
            $feet = $request->input('feet');
            $cart_item_identifier = [
                'product_id' => $dbProduct->id,
                'product_code' => $product_code,
                'source_type' => 'custom_item',
                'additional_info' => $additionalInfo,
                'quantity' => number_format($qty) * number_format($feet),
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
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function customItemcompleted()
    {
        $this->loadPageByType('custom_item_completed');

        return $this->render();
    }
}
