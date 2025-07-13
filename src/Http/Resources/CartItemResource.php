<?php

namespace Amplify\System\CustomItem\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
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
            'ItemNumber' => $this->product_code,
            'OrderQty' => (float) $this->quantity,
            'WarehouseID' => $this->warehouse_id ?? null,
        ];
    }
}
