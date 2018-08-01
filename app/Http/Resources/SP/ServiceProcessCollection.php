<?php

namespace App\Http\Resources\SP;

use App\Models\Employee;
use App\Models\Services\Contract_plan;
use App\Models\Services\Contract_planutil;
use App\Models\Utils\Service_type;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Log;

class ServiceProcessCollection extends ResourceCollection
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
            $col->type = Contract_planutil::findOrFail($col->type);
            if(!$col->contract_plan_id){
            //假如不是临时合同 依靠$col->contract_plan_id判断
                $col->type['name'] =  $col->type['name'].'(临时)';
            }

            $col->pm = collect(explode(",", $col->contract['PM']))->map(function($pm){
                if(!$pm) return null;
                else return Employee::findOrFail($pm);
            });
            $col->man = collect(explode(",", $col->man))->map(function($man){
                if(!$man) return null;
                else return Employee::findOrFail($man);
            });
            $col->customerTemp = $col->getRelations()['customer'][0];
            return new ServiceShowResourceForError($col);
        })->toArray();
    }
}
