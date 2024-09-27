<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class BrandRequest extends FormRequest
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
            'title' => 'required|string|max:255', // tiêu đề bắt buộc, kiểu chuỗi, tối đa 255 ký tự
            'image' => 'required', // image bắt buộc và phải là URL hợp lệ
            'status' => 'required',
            'parent_id' => 'nullable|integer|exists:categories,id', // parent_id là tùy chọn, phải là số nguyên và tồn tại trong bảng categories
            'create_by' => 'required|integer|exists:users,id', // create_by bắt buộc, phải là số nguyên và tồn tại trong bảng users
        ];
    }
    public function messages()
    {
        return [


            'title.required' => 'Tiêu đề là trường bắt buộc.',
            'title.string' => 'Tiêu đề phải là một chuỗi ký tự.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'image.url' => 'Hình ảnh phải là một đường dẫn',
            'status.numeric' => 'Trạng thái phải là một số',
            'parent_id.numeric' => 'Trạng thái phải là một số',

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
