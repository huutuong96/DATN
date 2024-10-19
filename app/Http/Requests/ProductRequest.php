<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ProductRequest extends FormRequest
{
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
            'name' => 'required|string',
            'description' => 'required|nullable|string|max:255',
            'infomation' => 'string',
            'price' => 'required|numeric',
            'sale_price' => 'numeric',
            'quantity' => 'required|numeric',
            'category_id'=> 'required|numeric',
            'brand_id'=> 'required|numeric',
            'shop_id'=> 'numeric',
        ];
    }

    public function messages()
{
    return [
        'name.required' => 'Trường tên là bắt buộc.',
        'name.string' => 'Tên phải là một chuỗi ký tự.',

        'slug.string' => 'Đường dẫn phải là một chuỗi ký tự.',

        'description.required' => 'Trường mô tả là bắt buộc.',
        'description.string' => 'Mô tả phải là một chuỗi ký tự.',
        'description.max' => 'Mô tả không được dài quá 255 ký tự.',

        'infomation.string' => 'Thông tin phải là một chuỗi ký tự.',

        'price.required' => 'Trường giá là bắt buộc.',
        'price.numeric' => 'Giá phải là một số.',

        'sale_price.numeric' => 'Giá bán phải là một số.',

        'quantity.required' => 'Trường số lượng là bắt buộc.',
        'quantity.numeric' => 'Số lượng phải là một số.',

        'sold_count.numeric' => 'Số lượng đã bán phải là một số.',

        'view_count.numeric' => 'Số lượng lượt xem phải là một số.',

        'parent_id.nullable' => 'Trường ID cha có thể bỏ qua.',

        'category_id.required' => 'Bạn phải nhập ID danh mục cho sản phẩm.',
        'category_id.numeric' => 'ID danh mục phải là một số',

        'brand_id.required' => 'Bạn phải nhập ID hãng cho sản phẩm.',
        'brand_id.numeric' => 'ID brand phải là một số.',

        'color_id.numeric' => 'Số lượng phải là một số.',
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
