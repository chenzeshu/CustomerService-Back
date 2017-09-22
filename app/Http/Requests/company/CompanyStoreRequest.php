<?php

namespace App\Http\Requests\company;

use Illuminate\Foundation\Http\FormRequest;

class CompanyStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //还是要看score 是否 大于 Scope::manager;
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
            'name' => 'required',
            'address' => 'required',
            'profession'=> 'required',
            'type'=> 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '必须填写公司名称',
            'address.required' => '必须填写公司地址',
            'profession.required' => '必须选择行业',
            'type.required' => '必须选择状态',
        ];
    }
}
