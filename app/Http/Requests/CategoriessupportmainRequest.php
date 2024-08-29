<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class CategoriessupportmainRequest extends FormRequest
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
            'content' => 'required|string|max:255',
            'status' => 'required',
            'index' => 'required|integer|min:0',
            'create_by' => 'nullable|integer|exists:users,id',
            'created_at' => 'nullable|date',
        ];
    }
    
    public function messages()
    {
        return [
            'content.required' => 'Trường nội dung là bắt buộc.',
            'content.string' => 'Nội dung phải là một chuỗi ký tự.',
            'content.max' => 'Nội dung không được vượt quá 255 ký tự.',
            'status.required' => 'Trường trạng thái là bắt buộc.',
            'index.required' => 'Trường chỉ số là bắt buộc.',
            'index.integer' => 'Chỉ số phải là một số nguyên.',
            'index.min' => 'Chỉ số không được nhỏ hơn 0.',
            'create_by.integer' => 'Người tạo phải là một số nguyên.',
            'create_by.exists' => 'Người tạo không tồn tại.',
            'created_at.date' => 'Ngày tạo phải là một ngày hợp lệ.',
        ];
    }
    
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        // Tạo phản hồi JSON cho lỗi xác thực
        $response = response()->json([
            'status' => false,
            'message' => 'Dữ liệu không hợp lệ',
            'errors' => $errors->toArray(),
        ], 422);

        throw new ValidationException($validator, $response);
    }
}
