<?php

namespace App\Http\Resources\Back;

use Illuminate\Http\Resources\Json\Resource;

class ServiceVerifyResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
//        asset(str_replace('public', 'storage', $this->doc[0]->path));
        return [
            'id' => $this->id,
            'contract' => $this->contract,
            'customer' => $this->getRelations()['customer'],
            'refer_man' => $this->getRelations()['refer_man'],
            'source' => $this->source,
            'desc1' => $this->desc1,
            'desc2' => $this->desc2,
            'workman' => $this->when(!is_null($this->workman), function(){
                return $this->workman;
            }),
            'project_manager' => $this->project_manager,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' =>  $this->updated_at->format('Y-m-d H:i:s'),
            'type' => $this->getRelations()['type'],
            'img' =>$this->doc[0] ,
            'allege' => $this->allege
        ];
    }
}
