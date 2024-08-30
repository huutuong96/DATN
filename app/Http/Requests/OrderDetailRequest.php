<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class OrderDetailRequest extends FormRequest
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
    public function rules()
    {
        return [
            'subtotal'   => 'required|numeric|min:0',
            'status'     => 'required',
            // 'order_id'   => 'required|integer|exists:orders,id',
            // 'product_id' => 'required|integer|exists:products,id',
            // 'create_by'  => 'required|integer|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'subtotal.required'   => 'Trường subtotal là bắt buộc.',
            'subtotal.numeric'    => 'Trường subtotal phải là một số.',
            'subtotal.min'        => 'Trường subtotal phải lớn hơn hoặc bằng 0.',
            'status.required'     => 'Trường status là bắt buộc.',
            'status.max'          => 'Trường status không được dài quá 255 ký tự.',
            'order_id.required'   => 'Trường order_id là bắt buộc.',
            'order_id.integer'    => 'Trường order_id phải là một số nguyên.',
            'order_id.exists'     => 'Giá trị order_id không tồn tại.',
            'product_id.required' => 'Trường product_id là bắt buộc.',
            'product_id.integer'  => 'Trường product_id phải là một số nguyên.',
            'product_id.exists'   => 'Giá trị product_id không tồn tại.',
            'create_by.required'  => 'Trường create_by là bắt buộc.',
            'create_by.integer'   => 'Trường create_by phải là một số nguyên.',
            'create_by.exists'    => 'Giá trị create_by không tồn tại.',
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
