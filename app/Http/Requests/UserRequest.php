<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fullname' => 'required|string|max:255',
            'password' => 'required',
            'email' => 'required',
        ];

    }
    public function messages()
    {
        return [
            'fullname.required' => 'Họ và tên là trường bắt buộc.',
            'fullname.string' => 'Họ và tên phải là một chuỗi ký tự.',
            'fullname.max' => 'Họ và tên không được vượt quá 255 ký tự.',

            'password.required' => 'Mật khẩu là trường bắt buộc.',
            'password.string' => 'Mật khẩu phải là một chuỗi ký tự.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',

            'email.required' => 'Email là trường bắt buộc.',
            'email.email' => 'Email phải là một địa chỉ email hợp lệ.',

        ];
    }

}
