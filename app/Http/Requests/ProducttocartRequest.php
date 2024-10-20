<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class ProducttocartRequest extends FormRequest
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
            'quantity' => 'required|integer|min:1',
            'status' => 'required',
            'cart_id' => 'required|exists:carts,id',
            'product_id' => 'required|exists:products,id',
           
        ];
    }
    public function messages()
    {
        return [
            'quantity.required' => 'Trường số lượng là bắt buộc.',
            'quantity.integer' => 'Số lượng phải là một số nguyên.',
            'status.required' => 'Trường trạng thái là bắt buộc.',
            'cart_id.required' => 'Trường giỏ hàng là bắt buộc.',
            'cart_id.exists' => 'Giỏ hàng không tồn tại.',
            'product_id.required' => 'Trường sản phẩm là bắt buộc.',
            'product_id.exists' => 'Sản phẩm không tồn tại.',
           
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
