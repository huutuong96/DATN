<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class VoucherRequest extends FormRequest
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
        'description' => 'nullable|string',
        'quantity' => 'required|integer|min:1',
        'limitValue'=>'required|integer|min:1',
        'min'=>'required|integer|min:1',
        'condition' => 'nullable',
        'ratio' => 'nullable|numeric|min:0', 
        'code' => 'nullable|string|max:100',
        'status' => 'required',
    ];
}

    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
   public function messages(): array
{
    return [
        'title.required' => 'Trường tiêu đề là bắt buộc.',
        'title.string' => 'Trường tiêu đề phải là một chuỗi ký tự.',
        'title.max' => 'Trường tiêu đề không được vượt quá 255 ký tự.',
        'description.string' => 'Trường mô tả phải là một chuỗi ký tự.',
        'quantity.required' => 'Trường số lượng là bắt buộc.',
        'quantity.integer' => 'Trường số lượng phải là một số nguyên.',
        'quantity.min' => 'Trường số lượng phải lớn hơn hoặc bằng 0.',
        'condition.string' => 'Trường điều kiện phải là một chuỗi ký tự.',
        'ratio.numeric' => 'Trường tỷ lệ phải là một số.',
        'limitValue.integer'=>'Trường số nguyên',
        'min'=>'Nhập số tiền đơn hàng tối thiểu được áp dụng',
        'code.string' => 'Trường mã phải là một chuỗi ký tự.',
        'code.max' => 'Trường mã không được vượt quá 100 ký tự.',
        'status.required' => 'Trường trạng thái là bắt buộc.',
        'status.string' => 'Trường trạng thái phải là một chuỗi ký tự.',
    ];
}

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        // Tạo phản hồi cho JSON trả về lỗi xác thực
        $response = response()->json([
            'status' => false,
            'message' => 'Dữ liệu không hợp lệ',
            'errors' => $errors->toArray(),
        ], 422);

        throw new ValidationException($validator, $response);
    }
}
