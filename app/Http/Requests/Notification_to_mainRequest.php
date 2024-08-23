<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class Notification_to_mainRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|integer|in:0,1',
            'create_by' => 'sometimes|required|integer',
            'update_by' => 'nullable|integer'
        ];
}

    public function messages()
    {
        return [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.string' => 'Tiêu đề phải là một chuỗi ký tự.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
    
            'description.string' => 'Mô tả phải là một chuỗi ký tự.',
    
            'image.file' => 'Tệp phải là một tệp hợp lệ.',
            'image.image' => 'Tệp phải là một ảnh.',
            'image.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, hoặc gif.',
            'image.max' => 'Ảnh không được vượt quá 2MB.',
    
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.integer' => 'Trạng thái phải là một số nguyên.',
            'status.in' => 'Trạng thái phải là 0 hoặc 1.',
    
            'create_by.required' => 'Người tạo là bắt buộc.',
            'create_by.integer' => 'Người tạo phải là một số nguyên.',
    
            'update_by.integer' => 'Người cập nhật phải là một số nguyên.',
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
