<?php

namespace App\Http\Resources\SP\Channel;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DeviceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function($item){
            return new DeviceResource($item);
        })->toArray();
    }
}
