<?php

namespace Amplify\System\CustomItem\Http\Controllers;

use Amplify\ErpApi\Facades\ErpApi;
use App\Http\Controllers\Controller;
use ErrorException;

class BaseController extends Controller
{
    public function getContactWarehouse()
    {
        $customer = ErpApi::getCustomerDetail();
        $warehouse_code = $customer->DefaultWarehouse ?: (customer_check() ? customer()?->warehouse?->code : config('amplify.frontend.guest_checkout_warehouse', null));

        if ($warehouse_code == null) {
            throw new ErrorException('Contact or Customer default warehouse is not configured.');
        }

        return $warehouse_code;
    }
}
