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
            "employee_id" => 'required',
        ];
    }

    public function messages()
    {
        return [
            "contractc_id.required" => '必须提交所属合同编号',
            "employee_id.required" => '必须提交客户申请人',
        ];
    }
}
