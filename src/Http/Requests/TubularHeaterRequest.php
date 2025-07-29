<?php

namespace Amplify\System\CustomItem\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TubularHeaterRequest extends FormRequest
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
            'configuration' => 'required',
            'product' => 'required',
            'price' => 'required',
            'qty' => 'required',
            'totalPrice' => 'required',
            'totalInc' => 'required',
            'OrderSpec' => 'required',
        ];
    }
}
