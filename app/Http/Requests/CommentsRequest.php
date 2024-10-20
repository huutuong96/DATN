<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class CommentsRequest extends FormRequest
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
            'rate' => 'required|integer|min:1|max:5',
            'status' => 'required',
            'parent_id' => 'nullable',
            'product_id' => 'required|exists:products,id',
          
        ];
    }
    public function messages()
    {
        return [
            'title.required' => 'Trường tiêu đề là bắt buộc.',
            'title.string' => 'Tiêu đề phải là chuỗi ký tự.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'content.required' => 'Trường nội dung là bắt buộc.',
            'content.string' => 'Nội dung phải là chuỗi ký tự.',
            'rate.required' => 'Trường đánh giá là bắt buộc.',
            'rate.integer' => 'Đánh giá phải là một số nguyên.',
            'rate.min' => 'Đánh giá phải từ 1 đến 5.',
            'rate.max' => 'Đánh giá phải từ 1 đến 5.',
            'status.required' => 'Trường trạng thái là bắt buộc.',
            'parent_id.exists' => 'Bình luận cha không tồn tại.',
            'product_id.required' => 'Trường sản phẩm là bắt buộc.',
            'product_id.exists' => 'Sản phẩm không tồn tại.',
            'user_id.required' => 'Trường người dùng là bắt buộc.',
            'user_id.exists' => 'Người dùng không tồn tại.',
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
