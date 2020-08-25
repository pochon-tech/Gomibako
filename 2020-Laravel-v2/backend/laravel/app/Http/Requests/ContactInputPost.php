<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactInputPost extends FormRequest
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
            'name' => 'required',
            'mail' => 'required|unique:contacts,mail',
            'tel' => 'required|max:15|not_regex:/[^0-9]/',
            'contents' => 'required',
        ];
    }

    // 省略可能
    public function messages(){
        return [
            'name.required' => '名前は必須です',
            'mail.required' => 'メールは必須です',
            'tel.required' => '電話番号は必須です',
            'tel.max' => '電話番号は最大15文字までです',
            'tel.not_regex' => '電話番号は半角数字で入力してください',
        ];
    }
}
