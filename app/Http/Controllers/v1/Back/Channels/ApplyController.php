<?php

namespace App\Http\Controllers\v1\Back\Channels;

use App\Exceptions\BaseException;
use App\Exceptions\Channels\OutOfTimeException;
use App\Exceptions\ScopeExp\ScopeExp;
use App\Http\Controllers\v1\Back\ApiController;
use App\Http\Helpers\JWTHelper;
use App\Http\Helpers\Params;
use App\Http\Helpers\Scope;
use App\Http\Repositories\ChannelRepo;
use App\Models\Channels\Channel;
use App\Models\Channels\Channel_apply;
use App\Models\Channels\Channel_operative;
use App\Models\Channels\Channel_real;
use App\Models\Channels\Contractc_plan;
use Illuminate\Http\Request;

//信道申请表
class ApplyController extends ApiController
{
    protected $channelRepo;

    function __construct(ChannelRepo $channelRepo)
    {
        $this->channelRepo = $channelRepo;
    }

    public function page($page, $pageSize)
    {
        $begin = ($page - 1) * $pageSize;
        $applies = Channel_apply::get_pagination($begin, $pageSize);
        $total = Channel_apply::get_total();
        list($tongxin, $jihua, $daikuan) = Channel_apply::get_cache();
        $data = [
            'data' => $applies,
            'total' => $total,
            'tongxin'=>$tongxin,
            'jihua' => $jihua,
            'daikuan'=>$daikuan
        ];

        return $this->res(200, '待审核', $data);
    }

    /**
     * 筛选临时信道合同的服务单, 要求检查权限 >=16
     */
    public function pageTemp($page, $pageSize, Request $request)
    {
        try{
            $user_scope = JWTHelper::getUserScope($request);
            if( $user_scope < Scope::TEMP_CONTRACT_SERVICE_MANAGER ){
                throw new ScopeExp();
            }
            $begin = ($page - 1) * $pageSize;
            $applies = Channel_apply::get_temp_pagination($begin, $pageSize);
            $total = count($applies);
            list($tongxin, $jihua, $daikuan) = Channel_apply::get_cache();
            $data = [
                'data' => $applies,
                'total' => $total,
                'tongxin'=>$tongxin,
                'jihua' => $jihua,
                'daikuan'=>$daikuan
            ];
            return $this->res(200, '待审核', $data);

        }catch (BaseException $e){
            $data = [
                'code' => $e->code,  //-4001
                'message' => $e->msg
            ];
            return $this->res(401, $e->msg, $data);
        }


    }

    /**
     * 补完申请并通过审核进入"运营调配"
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $model = Channel_apply::findOrFail($id);
        $re1 = $model->update($request->except(['channel_relations', 'contractc_plan']));
        $re2 = $model->channel()->update([
            'status'=>'运营调配'
        ]);

        if($re1 && $re2){
            return $this->res(2003, '审核通过');
        }
    }

    /**
     * 拒绝, 不通过审核
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function rej($id)
    {
        $re = Channel::findOrFail($id)->update([
            'status'=>'拒绝'
        ]);

        if($re){
            Channel::forget_cache();
            return $this->res(200, '已拒绝');
        }
    }

    /**
     * 更新运营表, 若不存在则创建
     * $id apply_id
     */
    public function updateOperative(Request $request, $id)
    {
        try{
            //todo 检查套餐余量是否充足, 不足则抛出OutOfTimeException
            $this->channelRepo->checkPlan($request);

            $model = Channel_operative::where('channel_apply_id', $request->channel_apply_id);
            if($model->first()){
                $re = $model->update($request->only(['id1','id2','id3','id4', 'remark','t1', 't2']));
            }else {
                $re = Channel_apply::findOrFail($request->channel_apply_id)->channel_operative()->create($request->only(['id1','id2','id3','id4', 'remark','t1', 't2']));
            }
            Channel::forget_cache();
            return $this->res(2003, '审核通过');

        } catch (OutOfTimeException $e){
            return $this->res($e->code, $e->msg);
        }

    }

    /**
     * $id apply_id
     * 更新实际运行表, 若不存在则创建
     */
    public function updateReal(Request $request, $id)
    {
        try{
            //todo true表示根据实际运行表单, 改变套餐已用量
            list($planModel, $curTime) = $this->channelRepo->checkPlan($request);
            $model = Channel_real::where('channel_apply_id', $request->channel_apply_id);
            //todo 如果第二个参数为真, 就进行套餐用量的修改
            $this->channelRepo->reCalPlan($model->first(), $curTime, $planModel);

            if($model->first()){
                $model->update($request->only(['checker_id','id1','id2','id3','id4', 'remark','t1', 't2']));
            }else {
                Channel_apply::findOrFail($request->channel_apply_id)->channel_real()->create(
                    $request->only(['checker_id','id1','id2','id3','id4', 'remark','t1', 't2'])
                );
            }
            Channel::forget_cache();
            return $this->res(2003, '审核通过');

        } catch (OutOfTimeException $e) {
            return $this->res($e->code, $e->msg);
        }
    }
}
