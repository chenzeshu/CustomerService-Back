<?php

namespace App\Http\Resources\SP;

use App\Models\Employee;
use Illuminate\Http\Resources\Json\ResourceCollection;

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
            $col->pm = collect(explode(",", $col->contract['PM']))->map(function($pm){
                return Employee::findOrFail($pm);
            });
            $col->customerTemp = $col->getRelations()['customer'][0];
            return new ServiceShowResourceForError($col);
        })->toArray();
    }
}
