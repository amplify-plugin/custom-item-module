<?php

namespace Amplify\System\CustomItem\Partials;

use Amplify\System\CustomItem\Traits\CustomItemERPService;
use Illuminate\Http\Request;

class ShelvingProduct
{
    use CustomItemERPService;

    private $diameter;

    private $finish;

    private $product;

    public function __construct(Request $request)
    {
        $this->diameter = $request->input('diameter');
        $this->finish = $request->input('finish');
        $this->product = $request->input('product');
    }

    private function products(): array
    {
        return [['item' => $this->product], ['item' => 'SHELF MINIMUMS HD'], ['item' => 'SHELF MINIMUMS SS'], ['item' => 'SHELF MINIMUMS']];
    }

    private function getProducts()
    {
        return $this->getProductsFromERP($this->products());
    }

    private function getProduct()
    {
        return $this->getProducts()->where('ItemNumber', $this->product)->first();
    }

    private function getShelfMinimumsSS()
    {
        return $this->getProducts()->select('Price', 'ItemNumber')->where('ItemNumber', 'SHELF MINIMUMS SS')->first();
    }

    private function getShelfMinimumsHD()
    {
        return $this->getProducts()->select('Price', 'ItemNumber')->where('ItemNumber', 'SHELF MINIMUMS HD')->first();
    }

    private function getShelfMinimum()
    {
        return $this->getProducts()->select('Price', 'ItemNumber')->where('ItemNumber', 'SHELF MINIMUMS')->first();
    }

    private function isHDDiameter()
    {
        return $this->diameter == 'HD';
    }

    private function isStainlessSteelNotHDDiameter()
    {
        return $this->diameter != 'HD' && $this->finish == 'SS';
    }

    private function isNoStainlessSteelandNotHDDiameter()
    {
        return $this->diameter != 'HD' && $this->finish != 'SS';
    }

    public function getPrice()
    {
        // dd($this->diameter);
        if ($this->isHDDiameter()) {
            return [
                'original_price' => $this->getProduct()['Price'] ?? 0,
                'minimum_price' => $this->getShelfMinimumsHD()['Price'] ?? 0,
                'product' => $this->product,
            ];
        }

        if ($this->isStainlessSteelNotHDDiameter()) {
            return [
                'original_price' => $this->getProduct()['Price'] ?? 0,
                'minimum_price' => $this->getShelfMinimumsSS()['Price'] ?? 0,
                'product' => $this->product,
            ];
        }

        if ($this->isNoStainlessSteelandNotHDDiameter()) {

            return [
                'original_price' => $this->getProduct()['Price'] ?? 0,
                'minimum_price' => $this->getShelfMinimum()['Price'] ?? 0,
                'product' => $this->product,
            ];
        }

        return [
            'original_price' => $this->getProduct()['Price'] ?? 0,
            'minimum_price' => $this->getShelfMinimum()['Price'] ?? 0,
            'product' => $this->product,
        ];
    }
}
