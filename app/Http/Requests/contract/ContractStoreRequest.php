<?php

namespace App\Http\Requests\contract;
//use Illuminate\Foundation\Exceptions\Handler;
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
//            'wocao'=>'required',
            "type1" => 'required',
            "type2" => 'required',
            "PM" => 'required',
            "time1" => 'required',
        ];
    }

    public function messages()
    {
        return [
          "company_id.required" => '必须提交单位编号',
//          'wocao.required'=>'错误在哪里?',
          "type1.required" => '必须提交合同类型',
          "type2.required" => '必须提交签订类型',
          "PM.required" => '必须选择项目经理',
          "time1.required" => '必须提交签订时间',
        ];
    }
}
