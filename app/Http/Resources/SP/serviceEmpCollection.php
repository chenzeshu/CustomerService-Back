<?php

namespace App\Http\Resources\SP;

use Illuminate\Http\Resources\Json\ResourceCollection;

class serviceEmpCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection
            ->reject(function($collecion){
                return $collecion->status == "离职";
            })
            ->map(function($collection){
                $collection->avatar = $collection->avatar == null ?
                "https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1515230693051&di=5ca0b0856aa1a2b1878c59e3228c66a3&imgtype=0&src=http%3A%2F%2Fwww.qqzhi.com%2Fuploadpic%2F2014-09-28%2F131049833.jpg"
                : $collection->avatar;
                return collect($collection)->only(['id', 'name', 'phone', 'avatar']);
            })
            ->toArray();
    }
}
