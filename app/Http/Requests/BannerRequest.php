<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class BannerRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'URL' => 'required|url', // kiểm tra định dạng URL
            'status' => 'required|numeric',
            'shop_id' => 'required|integer|exists:shops,id', // kiểm tra shop_id có tồn tại trong bảng shops
        ];
    }
    public function messages(){
            return [
                'shop_id' => 'required|integer|exists:shops,id', // kiểm tra shop_id có tồn tại trong bảng shops
                'title.required' => 'Vui lòng nhập nội dung cho title',
                'title.string' => 'Nội dung của title bắt buộc phải là một chuỗi',

                'content.required' => 'Vui lòng nhập nội dung cho content',
                'content.string' => 'Nội dung của content bắt buộc phải là một chuỗi',

                // 'URL.required' => 'vui lòng nhập đường dẫn cho url',
                // 'URL.url' => 'dữ liệu nhập vào không có định dạng của một đường dẫn',

                'status.numeric' => 'Kiểu dữ liệu của status phải là một số',

                // 'index.numeric' => 'Kiểu dữ liệu của index phải là một số',
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
