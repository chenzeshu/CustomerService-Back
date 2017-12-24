<?php

namespace App\Http\Requests\contractc;

use Illuminate\Foundation\Http\FormRequest;

class ContractcRequest extends FormRequest
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
            'company_id' => 'required',
            "PM" => 'required',
            "time" => 'required',
            "beginline" => 'required',
            "deadline" => 'required',
            "money" => 'required',
        ];
    }

    public function messages()
    {
        return [
            "company_id.required" => '必须提交单位编号',
            "PM.required" => '必须选择项目经理',
            "time.required" => '必须选择签订日期',
            "beginline.required" => '必须提交生效日期',
            "deadline.required" => '必须提交截止日期',
            "money.required" => '必须提交合同金额',
        ];
    }
}
