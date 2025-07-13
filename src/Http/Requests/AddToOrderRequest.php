<?php

namespace Amplify\System\CustomItem\Http\Controllers;

use Illuminate\Foundation\Http\FormRequest;

class AddToOrderRequest extends FormRequest
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
            'price' => 'required|numeric',
            'qty' => 'required|numeric',
            'depth' => 'required',
            'width' => 'required',
            'totalPrice' => 'required',
            'depthDisplay' => 'required',
            'widthDisplay' => 'required',
            'diameter' => 'required',
            'finish' => 'required',
            'product' => 'required',
            'OrderSpec' => 'required',
        ];
    }
}
