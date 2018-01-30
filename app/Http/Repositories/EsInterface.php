<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/30
 * Time: 10:07
 */

namespace App\Http\Repositories;


interface EsInterface
{
    /**
     * @param $content 模糊匹配的内容
     * @return mixed
     */
    public function esSearch($content);
}