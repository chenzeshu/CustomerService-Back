<?php

namespace App\Models\Problem;

use Illuminate\Database\Eloquent\Model;

/**
 * 故障类型表
 * Class ProblemType
 * @package App\Models\Problem
 */
class ProblemType extends Model
{
    protected $primaryKey = 'ptype_id';

    protected $guarded = [];

    /**
     * 得到故障分类所属的具体故障记录
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function problems(){
        return $this->hasMany(Problem::class, 'problem_type');
    }

    /**
     * 用于：得到故障分类所属的已解决的故障记录的ORM withCount
     */
    public function problemsFinished()
    {
        return $this->hasMany(Problem::class, 'problem_type');
    }
}
