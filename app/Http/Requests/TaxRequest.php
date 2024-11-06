<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class TaxRequest extends FormRequest
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
            'title' => 'required|string',
            'type' => 'required|string',
            'rate' => 'required|numeric|min:0|max:1', 
            'tax_number' => 'required|string',
            'status' => 'numeric'
        ];
    }

    public function messages(){
        return [
            'title.required' => 'Vui lòng nhập nội dung cho title',
            'title.string' => 'Nội dung của title bắt buộc phải là một chuỗi',

            'type.required' => 'Vui lòng nhập nội dung cho type',
            'type.string' => 'Nội dung của type bắt buộc phải là một chuỗi',

            'tax_number.required' => 'Vui lòng nhập nội dung cho tax_number',
            'tax_number.string' => 'Nội dung của tax_number bắt buộc phải là một chuỗi',

            'status.numeric' => 'Kiểu dữ liệu của status phải là một số',
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
