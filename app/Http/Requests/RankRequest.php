<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
class RankRequest extends FormRequest
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
            'status' => 'required',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string', // có thể không có
            'condition' => 'required|integer', 
            'value' => 'required|numeric|min:0', // giá trị phải là số dương
            'limitValue' => 'nullable|numeric|min:0', // giá trị giới hạn có thể không có hoặc là số dương
        ];
    }
    public function messages()
    {
        return [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.string' => 'Tiêu đề phải là chuỗi ký tự.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'description.string' => 'Mô tả phải là chuỗi ký tự.',
            'condition.required' => 'Điều kiện là bắt buộc.',
            // 'condition.string' => 'Điều kiện phải là chuỗi ký tự.',
            // 'condition.in' => 'Điều kiện phải là "new" hoặc "used".',
            'value.required' => 'Giá trị là bắt buộc.',
            'value.numeric' => 'Giá trị phải là một số.',
            'value.min' => 'Giá trị phải lớn hơn hoặc bằng 0.',
            'limitValue.numeric' => 'Giá trị giới hạn phải là một số.',
            'limitValue.min' => 'Giá trị giới hạn phải lớn hơn hoặc bằng 0.',
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
