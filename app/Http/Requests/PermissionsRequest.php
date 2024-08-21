<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
class PermissionsRequest extends FormRequest
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
            'premissionName' => 'required|string|max:255',

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
            'premissionName.required' => 'Trường "premissionName" là bắt buộc.',
            'premissionName.string' => 'Trường "premissionName" phải là một chuỗi ký tự.',
            'premissionName.max' => 'Trường "premissionName" không được vượt quá 255 ký tự.',

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
