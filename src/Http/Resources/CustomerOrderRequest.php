<?php

namespace Amplify\System\CustomItem\Http\Resources;

use Amplify\ErpApi\Facades\ErpApi;
use Illuminate\Foundation\Http\FormRequest;

class CustomerOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $customerDetails = ErpApi::getCustomerDetail();
        $po_rules = request('is_under_order_rule', false) ? 'nullable' : ($customerDetails->PoRequired == 'Y' ? 'required' : 'nullable');

        return [
            'order_type' => 'required',
            'customer_order_ref' => $po_rules,
            'shipping_method' => 'required',
            'order_note' => 'nullable',

            'address_name' => 'nullable',
            'address_country_code' => 'required_if:address_name,TEMP',
            'address_1' => 'required_if:address_name,TEMP',
            'address_city' => 'required_if:address_name,TEMP',
            'address_state' => 'required_if:address_name,TEMP',
            'address_zip_code' => 'required_if:address_name,TEMP',

            'shipping_number' => 'required_unless:address_name,TEMP',

            'card_token' => $customerDetails->CreditCardOnly == 'Y' ? 'required' : 'nullable',
        ];
    }
}
