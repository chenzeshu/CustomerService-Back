<?php

namespace App\Http\Requests\contract;

use Illuminate\Foundation\Http\FormRequest;

class ContractStoreRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_id' => 'required',
            'contract_id' => 'required',
            "type1" => 'required',
            "type2" => 'required',
            "PM" => 'required',
            "TM" => 'required',
            "time1" => 'required',
        ];
    }

    public function messages()
    {
        return [
          "company_id.required" => '必须提交单位编号',
          "contract_id.required" => '必须提交合同编号',
          "type1.required" => '必须提交合同类型',
          "type2.required" => '必须提交签订类型',
          "PM.required" => '必须选择项目经理',
          "TM.required" => '必须选择技术经理',
          "time1.required" => '必须提交签订时间',
        ];
    }
}
