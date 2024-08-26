<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class Vouchers extends FormRequest
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
            'type' => 'required|string',
            'status' => 'required',
            'code' => 'required'
        ];
    }

    public function messages(){
        return [
            'type.required' => 'Vui lòng nhập nội dung cho type',
            'type.string' => 'Nội dung của type bắt buộc phải là một chuỗi',
            'status.required' => 'Vui lòng nhập nội dung cho status',
            'code.required' => 'Vui lòng nhập nội dung cho code',
        ];
    }

    public function failedValidation(Validator $validator){
        $errors = $validator->errors();

        //tạo phản hồi cho json trả về lỗi xác thực
        $response = response()->json([
            'status' => false,
            'message' => 'Dữ liệu không hợp lệ',
            'errors' => $errors->toArray(),
        ], 422);

        throw new ValidationException($validator, $response);
    }
}
