<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class CouponRequest extends FormRequest
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
            'status' => 'required|numeric',
            'coupon_percentage' => 'numeric|min:0|max:100',
            'condition' => 'nullable|string|max:255',
            'create_by' => 'numeric'
        ];
    }

    public function messages()
    {
        return [
            'status.required' => 'Trường trạng thái là bắt buộc.',
            'status.numeric' => 'Trạng thái phải là một số.',

            '% coupon_percentage.numeric' => 'Phần trăm giảm giá phải là một số.',
            '% coupon_percentage.min' => 'Phần trăm giảm giá không được nhỏ hơn 0.',
            '% coupon_percentage.max' => 'Phần trăm giảm giá không được lớn hơn 100.',

            'condition.string' => 'Điều kiện phải là chuỗi ký tự.',
            'condition.max' => 'Điều kiện không được dài quá 255 ký tự.',

            'create_by.numeric' => 'ID người tạo phải là một số.',
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
