<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopRequest extends FormRequest
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
            'shop_name' => 'required|string',
            'pickup_address' => 'string',
            // 'image' => 'required|url',
            'tax_id' => 'required|number',
            'cccd' => 'required|string',
            'status' => 'number|required',
        ];
    }
    public function messages(){
        return [
            'shop_name.required' => 'Tên cửa hàng là bắt buộc.',
            'shop_name.string' => 'Tên cửa hàng phải là chuỗi ký tự.',

            'pickup_address.string' => 'Địa chỉ nhận hàng phải là chuỗi ký tự.',

            'image.required' => 'Hình ảnh là bắt buộc.',
            'image.url' => 'Hình ảnh phải là một đường dẫn URL hợp lệ.',

            'tax_id.required' => 'Mã số thuế là bắt buộc.',
            'tax_id.number' => 'Mã số thuế phải là một số.',

            'cccd.required' => 'Số CCCD là bắt buộc.',
            'cccd.string' => 'Số CCCD phải là chuỗi ký tự.',

            'status.required' => 'Trạng thái là bắt buộc.',
            'status.number' => 'Trạng thái phải là một số.',
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
