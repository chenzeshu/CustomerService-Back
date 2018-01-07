<?php

namespace App\Http\Controllers\v1\back\SP;

use App\Http\Controllers\v1\Back\ApiController;
use App\Http\Resources\SP\serviceCompanyCollection;
use App\Http\Resources\SP\serviceCompanyResource;
use App\Models\Company;
use Illuminate\Support\Facades\Cache;

class commonController extends ApiController
{
    public function searchCompany($keyword)
    {
        $data = Company::where('name', 'like',"%".$keyword."%")->get()->toArray();
        if(empty($data)){  //empty这个函数读起来有歧义, 其实是空返回true
            return $this->res(7004, '查无结果');
        }
        return $this->res(7003, '公司列表', $data);
    }

    public function searchContract($company_id)
    {
        $company = Company::with('employees')->findOrFail($company_id);
        $nowaTime = date('y-m-d', time());
        $data = $company->contracts()->get()->reject(function($contract) use ($nowaTime){
            //过滤已过期合同
            if($contract->time3 < $nowaTime){
                return true;
            }
        })->toArray();
        if(empty($data)){  //empty这个函数读起来有歧义, 其实是空返回true
            return $this->res(7004, '查无结果');
        }
        $service_types = $this->searchServiceType();
        $data = [
             $data, $service_types, new serviceCompanyResource($company)
        ];
        return $this->res(7003, '合同列表', $data);
    }


    public function searchServiceType()
    {
        $service_types = Cache::get("service_types");
        return $service_types;
    }
}
