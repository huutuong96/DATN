<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
class Blogrequest extends FormRequest
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
          
            'title' => 'required|string|max:255', // tiêu đề bắt buộc, kiểu chuỗi, tối đa 255 ký tự
           
        ];
    }
    
    public function messages()
    {
        return [
           
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.string' => 'Tiêu đề phải là chuỗi ký tự.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'description.string' => 'Mô tả phải là chuỗi ký tự.',
            'content.required' => 'Nội dung là bắt buộc.',
            'content.string' => 'Nội dung phải là chuỗi ký tự.',
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
