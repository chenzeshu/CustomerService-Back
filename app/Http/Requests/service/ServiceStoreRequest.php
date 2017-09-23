<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;

class ServiceStoreRequest extends FormRequest
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
            'contract_id' => 'required',
            'service_id' => 'required',
            "customer" => 'required',
            "time1" => 'required'
        ];
    }

    public function messages()
    {
        return [
            "contract_id.required" => '必须提交所属合同编号',
            "service_id.required" => '必须提交普通服务单编号',
            "customer.required" => '必须选择客户联系人',
            "time1.required" => '必须提交受理时间'
        ];
    }
}
