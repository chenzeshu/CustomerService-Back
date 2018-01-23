<?php

namespace App\Http\Controllers\v1\Back\SP;

use App\Exceptions\Channels\OutOfTimeException;
use App\Exceptions\SP\ChannelNoCheckerExp;
use App\Exceptions\SP\ChannelProcessingExp;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\v1\Back\ApiController;
use App\Http\Repositories\ChannelRepo;
use App\Http\Resources\SP\Channel\DeviceCollection;
use App\Models\Channels\Channel;
use App\Models\Channels\Contractc_plan;
use App\Models\Company;
use App\Models\Contractc;
use App\Models\Utils\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ChannelController extends ApiController
{
    protected $repo;

    function __construct(ChannelRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param $page 当前页数
     * @param $pageSize 每页数量
     * @param $emp_id 人员id
     * @param $status 信道单状态
     */
    public function page($page, $pageSize, $emp_id, $status)
    {
        $begin = ($page - 1) * $pageSize;
        if($status == "全部"){
            $data = Channel::with(['plans', "employee"])->where('employee_id', $emp_id)->offset($begin)->limit($pageSize)->get();
        }else{
            $data = Channel::with(['plans', "employee"])->where('employee_id', $emp_id)->where('status', $status)->offset($begin)->limit($pageSize)->get();
        }
        if(empty($data->toArray())){
            return $this->res(7004, '暂无更多', $data);
        }
        return $this->res(7003, $status.'列表', $data);
    }

    /**
     * 查看信道单细节
     * @param int $channel_id
     * @param string $status 信道单状态
     */
    public function showDetail($channel_id, $status)
    {
        $data = Channel::with(['contractc',
            'employee',
            'channel_applys' => function($query) use ($status){
                $arr = ['channel_relations.device.company',
                    'contractc_plan.plan'];
                switch ($status){
                    case "运营调配":
                        $arr = array_merge($arr, ['channel_operative' => function($query){
                            return $query->with(['tongxin', 'pinlv', 'jihua']);
                        }]);
                        break;
                    case "已完成" || "申述中":
                        $arr = array_merge($arr, ['channel_real'=> function($query){
                            return $query->with(['tongxin', 'pinlv', 'jihua','checker']);
                        }]);
                        break;
                    default: //待审核/拒绝
                        break;
                }
                return $query->with($arr);
            }])
            ->findOrFail($channel_id);
        return $this->res(7003, "状态".$status, $data);

        //1 项目经理 (服务单只跟着申请人走, 所以不上客户联系人)
        //2 套餐详情、参数
        //3 设备详情(型号, ip, 公司)
        //4 服务单本身信息
    }

    /**
     * 项目经理查看跟自己有关的信道服务单
     */
    public function pmRelation($pm_id)
    {
        return Contractc::with('channels')->whereIn('pm', [$pm_id])->get();
    }

    /**
     * 检索信道合同
     */
    public function searchContractc($company_id)
    {
        $nowaTime = date('y-m-d', time());
        $data = Company::findOrFail($company_id)
            ->contract_cs()
            ->get()
            ->reject(function($contract) use ($nowaTime){
                //过滤已过期合同
                if($contract->deadline < $nowaTime){
                    return true;
                }
            })->toArray();

        $params  = Cache::many(['tongxins','jihuas', 'zhantypes']);  //通信卫星 + 计划
        $params['starTypes'] = config('app.channel.stars');   //用星类型

        if(empty($data)){  //empty必须要数组 [],  collect也不行
            return $this->res(7004, '查无结果', $params);
        }
        $data = [
          'data' => $data,
          'params' => $params
        ];
        return $this->res(7003, '合同列表', $data);
    }

    /**
     * 搜索套餐
     */
    public function searchPlan($contractc_id)
    {
        $data = Contractc_plan::where('contractc_id', $contractc_id)
            ->get()
            ->reject(function($item){
                //先过滤掉无量套餐
                return $item->total === $item->use;
            })
            ->toArray();
        if(empty($data)){  //empty必须要数组 [],  collect也不行
            return $this->res(7004, '查无结果');
        }
        return $this->res(7003, '套餐列表', $data);
    }

    /**
     * 检索设备
     * @param $company_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchDevice($company_id)
    {
        $data = Device::where('company_id', $company_id)
            ->where('status', '!=', '停用')
            ->get();

        if($data->count() == 0){
            return $this->res(7004, '查无结果');
        }
        return $this->res(7003, '设备列表', new DeviceCollection($data));
    }

    /**
     *  提交申请事务, 创建信道单channel(待审核) + apply + relations
     */
    public function apply(Request $request)
    {
        DB::beginTransaction();
        try{
            //todo 整理传入参数
            $baseInfo = $request->baseInfo;  //第一页数据
            $data = $request->data;          //第二页数据
            //todo 调整开始/结束时间的格式
            $baseInfo['begin'] = $this->repo->transformTimeFormat($baseInfo['begin']);
            $baseInfo['end'] = $this->repo->transformTimeFormat($baseInfo['end']);
            //todo 检查套餐余量
            $this->repo->checkPlan($baseInfo['plan_id'], $baseInfo['begin'], $baseInfo['end']);
            //todo 检查本合同上次服务是否有人负责(无论是否临时, 若本次为第一次, 则无碍)
            $this->repo->checkChannel($baseInfo['contractc_id']);
            //todo 生成信道服务单号
            list($recordModel, $channel_id) = $this->repo->generateNumber(5);
            //fixme 这个increment源码是否有抛错, 会回滚吗? | 后期重构时并入generateNumber并仅在事务中调用
            $recordModel->increment('record');

            $channel = Contractc::findOrFail($baseInfo['contractc_id'])->channels()->create([
                'channel_id' => $channel_id,
                'employee_id' => $baseInfo['emp_id'],
                'type' => $baseInfo['starType'],
                'source' => 4 //小程序
            ]);

            $apply = $channel->channel_applys()->create([
                'id1' => $baseInfo['plan_id'], //用户套餐id
                'id2' => $baseInfo['sate_id'],
                'id3' => $baseInfo['pol_id'],
                't1' => $baseInfo['begin'],
                't2' => $baseInfo['end']
            ]);

            foreach ($data as $item){
                $apply->channel_relations()->create([
                    'company_id'=>$item['companyId'],
                    'device_id'=>$item['deviceId']
                ]);
            }
        }
        catch (ModelNotFoundException $e){
            DB::rollback();
            return $this->res(7004, '合同无效');
        }
        catch(OutOfTimeException $e){
            DB::rollback();
            return $this->res(7004, '超出用量');
        }
        catch (ChannelNoCheckerExp $e){
            DB::rollback();
            return $this->res(7004, $e->msg);
        }
        catch (ChannelProcessingExp $e){
            DB::rollback();
            return $this->res(7004, $e->msg);
        }
        DB::commit();
        return $this->res(7003, '申请成功');

    }
}
