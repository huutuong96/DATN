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
        'password' => 'required|string|min:8|max:30|confirmed', // confirmed yêu cầu password_confirmation field
        'email' => 'required|email|unique:users,email', // kiểm tra email duy nhất
        ];

    }
    public function messages()
    {
        return [
            'fullname.required' => 'Họ tên là bắt buộc.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.max' => 'Mật khẩu không được vượt quá 30 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Định dạng email không hợp lệ.',
            'email.unique' => 'Email đã tồn tại.',
        ];
    }

}
