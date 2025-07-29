<?php

namespace Amplify\System\CustomItem\Http\Controllers;

use Amplify\ErpApi\Facades\ErpApi;
use Amplify\System\CustomItem\Http\Requests\EvoporatorCoilRequest;
use Amplify\System\Factories\NotificationFactory;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Event;
use App\Models\State;

class EvaporatorCoilController extends Controller
{
    /**
     * @return [type]
     */
    public function getCountryList()
    {
        $country_codes = array_map(fn ($country) => $country['id'], config('amplify.basic.countries'));
        $countries = Country::select('iso2', 'id', 'name')->whereIn('id', $country_codes)->orderBy('name', 'ASC')->get();
        $states = State::select('iso2', 'country_id', 'country_code', 'name', 'id')->whereIn('country_id', $country_codes)->orderBy('name', 'ASC')->get();

        return response()->json([
            'countries' => $countries,
            'states' => $states,
        ]);
    }

    /**
     * @return [type]
     */
    public function store(EvoporatorCoilRequest $request)
    {
        $customer = ErpApi::getCustomerDetail();

        $data = [
            'address' => $request->address,
            'coil_is_coated' => $request->coil_is_coated,
            'copper_tube' => $request->copper_tube,
            'number_of_fins_per_inc' => $request->number_of_fins_per_inc,
            'number_of_tubes' => $request->number_of_tubes,
            'qty' => $request->qty,
            'contact_name' => $request->contact_name,
            'method_of_contact' => $request->method_of_contact,
            'company_name' => $request->company_name,
            'city' => $request->city,
            'country' => $request->country,
            'state' => $request->state,
            'zipcode' => $request->zipcode,
            'notes' => $request->note,
            'measurement_one_display' => $request->measurement_one_display,
            'measurement_two_display' => $request->measurement_two_display,
            'measurement_three_display' => $request->measurement_three_display,
            'measurement_four_display' => $request->measurement_four_display,
            'measurement_five_display' => $request->measurement_five_display,
            'measurement_six_display' => $request->measurement_six_display,
            'measurement_seven_display' => $request->measurement_seven_display,
        ];

        NotificationFactory::call([Event::CUSTOM_COIL_ORDER_RECEIVED], [
            'coil_data' => $data,
            'customer_info' => $customer,
        ]);

        return response()->json([
            'cart_summary' => route('frontend.carts.index'),
            'shop' => route('frontend.shop.index'),
            'message' => 'Success!',
        ]);
    }
}
