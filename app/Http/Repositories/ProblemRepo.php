<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/6
 * Time: 17:53
 */

namespace App\Http\Repositories;


use App\Models\Problem\Problem;

class ProblemRepo
{
    /**
     * 用于problem页面的筛选
     * @param $request
     * @return Problem|\Illuminate\Database\Eloquent\Builder | Problem模型的筛选结果，还没get
     */
    public function dealWithSearch($request)
    {
        $searchObj = $request->searchObj;

        if(!empty($searchObj['problem_type']['ptype_id'])){
            $problems = Problem::whereHas('problemType', function($query) use ($searchObj){
                $query->where('ptype_id', (int)$searchObj['problem_type']['ptype_id']);
            })
                ->with(['problemType', 'service']);
        } else {
            $problems = Problem::with(['problemType', 'service']);
        }

        if(!empty($searchObj['problem_step'])){
            $problems = $problems->where('problem_step', $searchObj['problem_step']);
        }

        if(!empty($searchObj['problem_urgency'])){
            $problems = $problems->where('problem_urgency', $searchObj['problem_urgency']);
        }

        if(!empty($searchObj['problem_importance'])){
            $problems = $problems->where('problem_importance', $searchObj['problem_importance']);
        }

        if(!empty($searchObj['problem_desc'])){
            $problems = $problems->where('problem_desc', $searchObj['problem_desc']);
        }

        return $problems;
    }
}