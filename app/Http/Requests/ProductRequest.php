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
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100', // SKU có thể là null hoặc là chuỗi tối đa 100 ký tự
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'infomation' => 'nullable|string',
            'price' => 'required|numeric|min:0', // Giá phải là số dương
            'sale_price' => 'nullable|numeric|min:0', // Giá giảm giá có thể null hoặc là số dương
            'image' => 'required|url', // Hình ảnh bắt buộc phải là URL
            'quantity' => 'required|integer|min:1', // Số lượng bắt buộc và phải là số nguyên lớn hơn 0
            'category_id' => 'required|integer|exists:categories,id', // category_id bắt buộc và phải tồn tại trong bảng categories
            'brand_id' => 'required|integer|exists:brands,id', // brand_id bắt buộc và phải tồn tại trong bảng brands
            'shop_id' => 'required|integer|exists:shops,id', // shop_id bắt buộc và phải tồn tại trong bảng shops
        ];
    }

    public function messages()
{
    return [
       'name.required' => 'Tên sản phẩm là bắt buộc.',
        'name.string' => 'Tên sản phẩm phải là chuỗi ký tự.',
        'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',
        'sku.string' => 'SKU phải là chuỗi ký tự.',
        'sku.max' => 'SKU không được vượt quá 100 ký tự.',
        'slug.string' => 'Slug phải là chuỗi ký tự.',
        'slug.max' => 'Slug không được vượt quá 255 ký tự.',
        'price.required' => 'Giá là bắt buộc.',
        'price.numeric' => 'Giá phải là một số.',
        'price.min' => 'Giá phải lớn hơn hoặc bằng 0.',
        'sale_price.numeric' => 'Giá giảm giá phải là một số.',
        'sale_price.min' => 'Giá giảm giá phải lớn hơn hoặc bằng 0.',
        'image.required' => 'Hình ảnh là bắt buộc.',
        'image.url' => 'Hình ảnh phải là một URL hợp lệ.',
        'quantity.required' => 'Số lượng là bắt buộc.',
        'quantity.integer' => 'Số lượng phải là một số nguyên.',
        'quantity.min' => 'Số lượng phải lớn hơn hoặc bằng 1.',
        'category_id.required' => 'Danh mục là bắt buộc.',
        'category_id.integer' => 'Danh mục phải là một số nguyên.',
        'category_id.exists' => 'Danh mục không tồn tại.',
        'brand_id.required' => 'Thương hiệu là bắt buộc.',
        'brand_id.integer' => 'Thương hiệu phải là một số nguyên.',
        'brand_id.exists' => 'Thương hiệu không tồn tại.',
        'shop_id.required' => 'Cửa hàng là bắt buộc.',
        'shop_id.integer' => 'Cửa hàng phải là một số nguyên.',
        'shop_id.exists' => 'Cửa hàng không tồn tại.',
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
