<?php

namespace Amplify\System\CustomItem\Http\Controllers;

use Amplify\System\Backend\Models\CustomProduct;
use Amplify\System\Backend\Models\Product;
use Amplify\System\CustomItem\Http\Requests\TubularHeaterRequest;
use Amplify\System\CustomItem\Traits\CustomItemERPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TubularHeaterController extends BaseController
{
    use CustomItemERPService;

    /**
     * Get Strips Replacement Product From JSON File
     *
     * @return [type]
     */
    public function getProducts($num)
    {
        $lowPoint = $num - 10;
        if ($lowPoint < 1) {
            $lowPoint = 1;
        }
        $highPoint = $num + 10;
        $tubularHeaterData = CustomProduct::whereType('tubular-heaters')->firstOrFail();
        $tubularHeaterContent = json_decode($tubularHeaterData->value);
        $products = $tubularHeaterContent->products;
        $productsInRange = array_filter($products, function ($item) use ($lowPoint, $highPoint) {
            $length = intval($item->length);

            return $length >= $lowPoint && $length <= $highPoint;
        });

        return response()->json($productsInRange);
    }

    /**
     * Get Strip replacement Product Price
     *
     * @param  mixed  $code
     * @return [type]
     */
    public function getProductPrice($code)
    {
        try {
            $product = $this->getProductDetailsFromERP(code: $code);

            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * StripCurtains Replacement add to cart
     *
     * @param  Request  $request
     * @return [type]
     */
    public function addToCart(TubularHeaterRequest $request)
    {

        DB::beginTransaction();
        try {
            $cart = getOrCreateCart();
            $dbProduct = Product::with('productImage')->whereProductCode($request->input('product'))->first();
            $product_code = $request->input('product');
            $warehouse = $this->getContactWarehouse();
            $additionalInfo = json_encode([
                'configuration' => $request->configuration,
                'product' => $request->product,
                'qty' => $request->qty,
                'totalPrice' => $request->totalPrice,
                'totalInc' => $request->totalInc,

                // common value
                'uom_qty' => $request->qty,
                'price' => $request->price,
                'total_price' => $request->totalPrice,
                'OrderSpec' => $request->OrderSpec,
                'uom' => $request->uom,
            ]);

            $cart_item_identifier = [
                'product_id' => $dbProduct->id,
                'product_code' => $product_code,
                'source_type' => 'custom_item',
                'additional_info' => $additionalInfo,
                'quantity' => $request->input('qty'),
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
