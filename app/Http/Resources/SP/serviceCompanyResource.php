<?php

namespace App\Http\Resources\SP;

use Illuminate\Http\Resources\Json\Resource;

class serviceCompanyResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'address' => $this->address,
            'employees' => new serviceEmpCollection($this->whenLoaded('employees')),
        ];
    }
}
