<?php

namespace Amplify\System\CustomItem\Http\Controllers;

use Illuminate\Foundation\Http\FormRequest;

class GasketRequest extends FormRequest
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
            'depth' => 'required',
            'width' => 'required',
            'price' => 'required',
            'side' => 'required',
            'qty' => 'required',
            'depth_display' => 'required',
            'width_display' => 'required',
            'product' => 'required',
            'OrderSpec' => 'required',
            'totalFeet' => 'required',
            'totalQty' => 'required',
            'totalPrice' => 'required',
        ];
    }
}
