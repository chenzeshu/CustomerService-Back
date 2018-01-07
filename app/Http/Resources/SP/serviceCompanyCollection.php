<?php

namespace App\Http\Resources\SP;

use Illuminate\Http\Resources\Json\ResourceCollection;

class serviceCompanyCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return
            $this->collection
            ->map(function($collection){
                $collection->avatar = $collection->avatar == null ?
                    "https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1515230693051&di=5ca0b0856aa1a2b1878c59e3228c66a3&imgtype=0&src=http%3A%2F%2Fwww.qqzhi.com%2Fuploadpic%2F2014-09-28%2F131049833.jpg"
                    : $collection->avatar;
                if($collection->employees->count()>0){
                    $collection->empArr = new serviceEmpCollection($collection->employees);
                    return collect($collection)->only(['name', 'phone', 'avatar', 'empArr']);
                }
                return collect($collection)->only(['name', 'phone', 'avatar']);
            })
            ->toArray();
    }
}
