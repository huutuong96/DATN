<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class supportmainRequest extends FormRequest
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
            'content' => 'required|string|max:1000', // Bắt buộc, kiểu chuỗi, tối đa 1000 ký tự
            'status' => 'required',
            'index' => 'required|integer', // Bắt buộc, kiểu số nguyên
            'category_support_id' => 'required|exists:category_supports,id', // Bắt buộc, phải tồn tại trong bảng category_supports
        ];
    } public function messages(){
        return [
            'content.required' => 'Nội dung là bắt buộc.',
            'content.string' => 'Nội dung phải là một chuỗi.',
            'content.max' => 'Nội dung không được vượt quá 1000 ký tự.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.integer' => 'Trạng thái phải là một số nguyên.',
            'status.in' => 'Trạng thái chỉ có thể là 0 hoặc 1.',
            'index.required' => 'Chỉ số là bắt buộc.',
            'index.integer' => 'Chỉ số phải là một số nguyên.',
            'category_support_id.required' => 'ID danh mục hỗ trợ là bắt buộc.',
            'category_support_id.exists' => 'ID danh mục hỗ trợ không tồn tại.',
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

