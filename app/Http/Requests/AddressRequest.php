<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
class AddressRequest extends FormRequest
{
    /**
     * Xác thực người dùng có quyền thực hiện request này không.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Thay đổi điều này nếu bạn cần kiểm tra quyền truy cập
    }

    /**
     * Các quy tắc xác thực cho request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'address' => 'required|string|max:255',

        ];
    }

    /**
     * Các thông báo lỗi cho các quy tắc xác thực.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'address.required' => 'Trường "address" là bắt buộc.',
            'address.string' => 'Trường "address" phải là một chuỗi ký tự.',
            'address.max' => 'Trường "address" không được vượt quá 255 ký tự.',

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
