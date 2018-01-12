<?php

namespace App\Http\Resources\Back;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ServiceVerifyCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function($col){
            return new ServiceVerifyResource($col);
        })->toArray();

    }
}
