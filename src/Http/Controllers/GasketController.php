<?php

namespace Amplify\System\CustomItem\Http\Controllers;

use Amplify\System\CustomItem\Http\Controllers\GasketRequest;
use Amplify\System\CustomItem\Http\Resources\ProductResource;
use Amplify\System\CustomItem\Traits\CustomItemERPService;
use App\Models\Cart;
use App\Models\CustomProduct;
use App\Models\Product;
use DB;
use Illuminate\Http\Request;

class GasketController extends BaseController
{
    use CustomItemERPService;

    private function getProductFromJson(): array
    {
        $gasketProducts = CustomProduct::whereType('gasket')->firstOrFail();

        return json_decode($gasketProducts->value, true);
    }

    /**
     * Get Strips Replacement Product From JSON File
     *
     * @return [type]
     */
    public function getProductType()
    {
        $gasketProducts = $this->getProductFromJson();

        return response()->json($gasketProducts);
    }

    /**
     * Get Strips Replacement Product From JSON File
     *
     * @return [type]
     */
    public function getPrice(Request $request)
    {
        $code = $request->input('code');
        $product = $this->getProductDetailsFromERP($code);

        return response()->json($product);
    }

    /**
     * Get Strips Replacement Product From JSON File
     *
     * @return [type]
     */
    public function getProductsList($type)
    {
        $gasketProducts = $this->getProductFromJson();
        if (! empty($type)) {
            $products = $gasketProducts[$type];

            return response()->json($products);
        } else {
            return response()->json(['error' => 'Type not found!'], 404);
        }
    }

    public function getProducts(Request $request)
    {
        $dbProduct = Product::with('productImage')->whereIn('product_code', $request->input('product'))->get();

        return ProductResource::collection($dbProduct);

    }

    public function getProfileList(Request $request)
    {
        $gasketProducts = $this->getProductFromJson();
        $type = $request->input('type');
        $product = $request->input('product');
        if ($type && $product) {
            $products = $gasketProducts[$type][$product];

            return response()->json($products);
        } else {
            return response()->json([
                'error' => 'Something went wrong',
            ]);
        }
    }

    public function getProfiles(Request $request)
    {
        $dbProduct = Product::with(['productImage', 'attributes'])->whereIn('product_code', $request->input('product'))->get();

        return ProductResource::collection($dbProduct);
    }

    /**
     * Get Strip replacement Product Price
     *
     * @param  mixed  $code
     * @return [type]
     */
    public function getStripProductPrice($code)
    {
        $product = Product::where('product_code', $code)->firstOrFail();

        return response()->json($product);
    }

    /**
     * StripCurtains Replacement add to cart
     *
     * @param  Request  $request
     * @return [type]
     */
    public function addToCart(GasketRequest $request)
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
                'side' => $request->side,
                'qty' => $request->qty,
                'depth_display' => $request->depth_display,
                'width_display' => $request->width_display,
                'product_code' => $request->product,
                'totalFeet' => $request->totalFeet,
                'totalQty' => $request->totalQty,
                'foam_fill' => $request->foam_fill,
                'door_hinged' => $request->door_hinged,

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
                'quantity' => number_format($request->input('totalFeet')) * number_format($request->qty),
                'product_warehouse_code' => $warehouse ?? '',
                'warehouse_id' => $warehouse ?? '',
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
