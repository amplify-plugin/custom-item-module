<?php

namespace Amplify\System\CustomItem\Http\Controllers;

use Illuminate\Foundation\Http\FormRequest;

class CuttingBoardAddToCartRequest extends FormRequest
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
            'selling_price' => 'required',
            'qty' => 'required|numeric',
            'product' => 'required',
            'OrderSpec' => 'required',
            'product' => 'required',
            'totalSquareInches' => 'required',
            'totalPrice' => 'required',
            'thickness' => 'required',
        ];
    }
}
