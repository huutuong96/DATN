<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ProducttoshopRequest extends FormRequest
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
            'url_share' => 'nullable|url',
            'status' => 'required',
            'product_id' => 'required|exists:products,id',
            'shop_id' => 'required|exists:shops,id',
        ];
    }
    public function messages()
    {
        return [
            
        'url_share.url' => 'Trường url_share phải là một URL hợp lệ.',
        'status.required' => 'Trường trạng thái là bắt buộc.',
        'product_id.required' => 'Trường sản phẩm là bắt buộc.',
        'product_id.exists' => 'Sản phẩm không tồn tại.',
        'shop_id.required' => 'Trường cửa hàng là bắt buộc.',
        'shop_id.exists' => 'Cửa hàng không tồn tại.',
        'create_by.required' => 'Trường người tạo là bắt buộc.',
        'create_by.exists' => 'Người tạo không tồn tại.',
        'update_by.exists' => 'Người cập nhật không tồn tại.'

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
