<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class CategoriesRequest extends FormRequest
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
            'index' => 'required|integer',
            'image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|integer|in:0,1',
            'parent_id' => 'nullable|integer',
            'category_id_main' => 'integer'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.string' => 'Tiêu đề phải là chuỗi ký tự.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',

            'index.required' => 'Chỉ mục (index) là bắt buộc.',
            'index.integer' => 'Chỉ mục (index) phải là số nguyên.',

            'image.nullable' => 'Hình ảnh có thể để trống.',
            'image.file' => 'Hình ảnh phải là một file hợp lệ.',
            'image.image' => 'Hình ảnh phải có định dạng hình ảnh.',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, hoặc gif.',
            'image.max' => 'Hình ảnh không được vượt quá 2MB.',

            'status.required' => 'Trạng thái là bắt buộc.',
            'status.integer' => 'Trạng thái phải là số nguyên.',
            'status.in' => 'Trạng thái chỉ có thể là 0 hoặc 1.',

            'parent_id.nullable' => 'Parent ID có thể để trống.',
            'parent_id.integer' => 'Parent ID phải là số nguyên.',

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
