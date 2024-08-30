<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Http\FormRequest;

class ProgramtoshopRequest extends FormRequest
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
            'program_id' => 'required|integer|exists:programs,id',
            'shop_id'    => 'required|integer|exists:shops,id',
        ];
    }
    public function messages()
    {
        return [
           'program_id.required' => 'Trường program_id là bắt buộc.',
            'program_id.integer'  => 'Trường program_id phải là một số nguyên.',
            'program_id.exists'   => 'Giá trị program_id không tồn tại.',
            'shop_id.required'    => 'Trường shop_id là bắt buộc.',
            'shop_id.integer'     => 'Trường shop_id phải là một số nguyên.',
            'shop_id.exists'      => 'Giá trị shop_id không tồn tại.',
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
