<?php

namespace Amplify\System\CustomItem\Traits;

use Amplify\ErpApi\Facades\ErpApi;

trait CustomItemERPService
{
    public function getProductDetailsFromERP(string $code)
    {

        try {
            if (has_erp_customer()) {
                $warehouses = ErpApi::getWarehouses();
                $warehouseString = $warehouses->reduce(function ($previous, $current) {
                    return $previous.$current->WarehouseNumber;
                }, '');
                $product = ErpApi::getProductPriceAvailability([
                    'items' => [['item' => $code, 'warehouse' => $warehouseString]],
                ])?->first();

                return $product;
            }

            return [
                'status' => 'error',
                'message' => 'User not found!',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function getProductsFromERP(array $items)
    {
        try {
            if (has_erp_customer()) {
                $products = ErpApi::getProductPriceAvailability([
                    'items' => $items,
                ]);

                return $products;
            }

            return [
                'status' => 'error',
                'message' => 'User not found!',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}
