<?php

namespace App\Http\Requests;


class UserRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required', 'unique:users,email'],
            'name' => ['required'],
            'password' => ['required', 'max:16', 'min:6']
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => '邮箱已经存在',
            'password.required' => '密码不能为空',
            'password.max' => '密码长度不能超过16个字符',
            'name.max' => '名字不能超过12个字符',
            'password.min' => '密码长度不能小于6个字符'
        ];
    }
}
