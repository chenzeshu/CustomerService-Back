<?php

namespace App\Http\Resources\SP\Channel;

use Illuminate\Http\Resources\Json\Resource;

class DeviceResource extends Resource
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
          'id' => $this->id,
          'name' => $this->device_id .":". $this->ip
        ];
    }
}
