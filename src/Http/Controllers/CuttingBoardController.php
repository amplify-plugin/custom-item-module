<?php

namespace Amplify\System\CustomItem\Http\Controllers;

use Amplify\System\CustomItem\Http\Requests\CuttingBoardAddToCartRequest;
use Amplify\System\CustomItem\Traits\CustomItemERPService;
use App\Models\CustomProduct;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CuttingBoardController extends BaseController
{
    use CustomItemERPService;

    /**
     * Get Strips Replacement Product From JSON File
     *
     * @return \Illuminate\Http\JsonResponse [type]
     */
    public function getProducts(): JsonResponse
    {
        $stripReplacementData = CustomProduct::whereType('cutting-board')->firstOrFail();

        return response()->json($stripReplacementData->value);
    }

    /**
     * Get Strip replacement Product Price
     *
     * @param  mixed  $code
     * @return [type]
     */
    public function getStripProductPrice($code)
    {
        $product = $this->getProductDetailsFromERP($code);

        return response()->json($product);
    }

    /**
     * StripCurtains Replacement add to cart
     *
     * @param  Request  $request
     * @return [type]
     */
    public function addToCart(CuttingBoardAddToCartRequest $request)
    {
        DB::beginTransaction();
        try {
            $cart = getOrCreateCart();
            $dbProduct = Product::with('productImage')->whereProductCode($request->input('product'))->first();
            $product_code = $request->input('product');
            $warehouse = $this->getContactWarehouse();
            $unitprice = $this->calculateUnitPrice($request);
            $additionalInfo = json_encode([
                'depth' => $request->depth,
                'width' => $request->width,
                'product_code' => $request->product,
                'total_square_inches' => $request->totalSquareInches,
                'qty' => $request->qty,
                'thickness' => $request->thickness,
                'uom_qty' => $request->qty,
                'price' => $request->selling_price,
                'total_price' => $request->totalPrice,
                'OrderSpec' => $request->OrderSpec,
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
                'unitprice' => $request->input('selling_price'),
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

    public function calculateUnitPrice($request): float
    {
        $price = 0.00;
        if ($request->input('totalPrice') != 0 && $request->input('qty') != 0) {
            $price = $request->input('totalPrice') / $request->input('qty');
        }

        return $price;
    }
}
