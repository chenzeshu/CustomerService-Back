<?php

namespace App\Http\Controllers\v1\back\SP;

use App\Dao\ServiceDAO;
use App\Http\Controllers\v1\Back\ApiController;
use App\Http\Repositories\CompanyRepo;
use App\Http\Resources\SP\Channel\CompanyCollection;
use App\Http\Resources\SP\serviceCompanyCollection;
use App\Http\Resources\SP\serviceCompanyResource;
use App\Models\Company;
use App\Models\Doc;
use App\Models\Services\Contract_plan;
use App\Models\Services\Contract_planutil;
use App\Models\Services\Service;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class commonController extends ApiController
{
    protected $repo;

    function __construct(CompanyRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * 保修检索公司
     * @param $keyword 公司名关键字
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchCompany($keyword)
    {
//        $data = Company::where('name', 'like',"%".$keyword."%")->get()->toArray();
        $data = $this->repo->esSearch($keyword);

        if(empty($data)){  //empty这个函数读起来有歧义, 其实是空返回true
            return $this->res(7004, '查无结果');
        }
        return $this->res(7003, '公司列表', $data);
    }

    /**
     * 保修检索合同
     * @param $company_id
     * @return \Illuminate\Http\JsonResponse
     */
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

        $service_types = ServiceDAO::getServiceTypes();
        $data = [
             $data, $service_types, new serviceCompanyResource($company)
        ];
        return $this->res(7003, '合同列表', $data);
    }

    /**
     * 检索套餐
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchMeal(Request $request)
    {
        $typeArr = [];
        $plans = Contract_plan::where('contract_id', $request->contractId)
            ->get()
            ->map(function ($meal) use (&$typeArr){
                $typeArr[] = $meal->plan_id;
                return $meal;
            });
        $data = Contract_planutil::findOrFail($typeArr);

        if(count($data) == 0){
            return $this->res(-7003, '本合同下没有套餐');
        } else {
            //todo 做套餐余量查询
            $plans = $plans->filter(function ($meal){
                if($meal->use >= $meal->total){
                    //没有余量
                    return false;
                }
                return true;
            });
            if(count($plans) > 0){
                return $this->res(7003, '含套餐的类型列表', $data);
            } else {
                return $this->res(7003, '所有套餐超限，请联系客服', []);
            }

        }




    }

    /**
     * 申请完成: 可以不上传图片
     * 调取时, 拿到服务单的file内容数组, 拿出里面有public/apply的即可
     * @param $service_id
     * @param Request $request
     * @return string
     */
    public function upload($service_id, Request $request)
    {
        $doc_id = "";

        if($request->hasFile('wxFile')){
            $file = $request->file('wxFile');
            $file_path = Storage::putFile('public/apply', new File($file), 'public');

            $doc = Doc::create([
                'name' => $request->title,
                'path' => $file_path,
            ]);

            $doc_id = $doc->id;
        }

        $model = Service::findOrFail($service_id);
        $model->update([
            'status' => '申请完成',
            'document' => ltrim( $model->document . "," . $doc_id, ","),
            'desc1' => $request->desc1,
            'desc2' => $request->desc2
        ]);

        return $this->res(7004, '申请完毕');
    }


}
