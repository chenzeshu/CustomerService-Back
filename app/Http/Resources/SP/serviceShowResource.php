<?php

namespace App\Http\Resources\SP;

use App\Models\Utils\Service_type;
use Illuminate\Http\Resources\Json\Resource;

class serviceShowResource extends Resource
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
           'status'=> $this->status,
           'time1' => $this->time1 ? $this->time1 : '未派单',
           'time2' => $this->time2 ? $this->time2 : '未解决',
           'type' => $this->getRelations()['type'],
           'customer' =>new serviceEmpResource($this->customer),
           'desc1' => $this->desc1 ? $this->desc1 : '未填写',
           'desc2' => $this->desc2 ? $this->desc2 : '未填写',
           'man' => $this->man ? new serviceEmpCollection($this->man) : '未派单',
           'company' => new serviceCompanyResource($this->contract->company),
           'question' => $this->question,
           'img' => $this->doc[0],
       ];
    }
}
