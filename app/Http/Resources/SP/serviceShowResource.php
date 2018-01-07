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
           'time1' => $this->time1,
           'time2' => $this->time2,
           'type' => $this->type,
           'customer' =>new serviceEmpResource($this->customer),
           'desc1' => $this->desc1,
           'desc2' => $this->desc2,
           'pm' => new serviceEmpCollection($this->pm),
           'company' => new serviceCompanyResource($this->contract->company),
       ];
    }
}
