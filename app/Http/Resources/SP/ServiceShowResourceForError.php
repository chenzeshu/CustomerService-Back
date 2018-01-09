<?php

namespace App\Http\Resources\SP;

use App\Models\Utils\Service_type;
use Illuminate\Http\Resources\Json\Resource;

class ServiceShowResourceForError extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $this->type = Service_type::findOrFail($this->type)->name;
        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'status'=> $this->status,
            'time1' => $this->time1,
            'time2' => $this->time2,
            'type' => $this->type,
            'customer' =>new serviceEmpResource($this->customerTemp),
            'desc1' => $this->desc1,
            'desc2' => $this->desc2,
            'pm' => new serviceEmpCollection($this->pm),
            'company' => new serviceCompanyResource($this->contract->company),
            'man' => $this->when(!empty($this->man->toArray()), new serviceEmpCollection($this->man))
        ];
    }
}
