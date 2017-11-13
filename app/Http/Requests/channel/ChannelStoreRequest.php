<?php

namespace App\Http\Requests\channel;

use Illuminate\Foundation\Http\FormRequest;

class ChannelStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'contractc_id' => 'required',
            'channel_id' => 'required',
            "customer" => 'required',
        ];
    }

    public function messages()
    {
        return [
            "contractc_id.required" => '必须提交所属合同编号',
            "channel_id.required" => '必须提交信道服务单编号',
            "customer.required" => '必须提交客户申请人',
        ];
    }
}
