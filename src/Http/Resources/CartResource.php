<?php

namespace Amplify\System\CustomItem\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            $this->cartItems
                ? CartItemResource::collection($this->cartItems)
                : [],
        ];
    }
}
