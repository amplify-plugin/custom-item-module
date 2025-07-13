<?php

namespace Amplify\System\CustomItem\Http\Controllers;

use Illuminate\Foundation\Http\FormRequest;

class EvoporatorCoilRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'address' => 'required',
            'city' => 'required',
            'coil_is_coated' => 'required',
            'company_name' => 'required',
            'contact_name' => 'required',
            'copper_tube' => 'required',
            'country' => 'required',
            'measurement_five_display' => 'required',
            'measurement_four_display' => 'required',
            'measurement_one_display' => 'required',
            'measurement_seven_display' => 'required',
            'measurement_six_display' => 'required',
            'measurement_three_display' => 'required',
            'measurement_two_display' => 'required',
            'method_of_contact' => 'required',
            'number_of_fins_per_inc' => 'required',
            'number_of_tubes' => 'required',
            'qty' => 'required',
            'state' => 'required',
            'zipcode' => 'required',
        ];
    }
}
