<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class FAQRequest extends FormRequest
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
            'content' => 'required|string',
            'status' => 'numeric',
            'index' => 'numeric'
        ];
    }

    public function messages(){
        return [
            'title.required' => 'Vui lòng nhập nội dung cho title',
            'title.string' => 'Nội dung của title bắt buộc phải là một chuỗi',

            'content.required' => 'Vui lòng nhập nội dung cho content',
            'content.string' => 'Nội dung của content bắt buộc phải là một chuỗi',

            'status.numeric' => 'Kiểu dữ liệu của status phải là một số',

            'index.numeric' => 'Kiểu dữ liệu của index phải là một số',
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
