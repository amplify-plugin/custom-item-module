<?php

namespace Amplify\System\CustomItem\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'product_code' => $this->product_code,
            'thumbnail' => $this->thumbnail ?? $this->productImage?->main,
            'price' => $this->selling_price,
            'attributes' => $this->attributes->map(function ($data) {
                return [
                    'name' => $data->local_name ?? $data->local_name,
                    'value' => $data->pivot?->attribute_value ?? $data->pivot->attribute_value,
                ];
            }),
        ];
    }
}
