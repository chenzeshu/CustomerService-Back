<?php

namespace App\Http\Resources\SP;

use Illuminate\Http\Resources\Json\Resource;

class serviceEmpResource extends Resource
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
          'phone' => $this->phone,
          'status' => $this->status,
          'avatar' => $this->when(is_null($this->avatar), function(){
              return '404';
          }),
        ];
    }
}
