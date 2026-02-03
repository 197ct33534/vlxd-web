<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Cho phép mọi user upload (hoặc check auth check() nếu cần)
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image' => [
                'required',
                'image',                // Phải là file ảnh
                'mimes:jpeg,png,jpg,webp', // Chỉ cho phép định dạng này
                'max:2048',             // Tối đa 2MB (2048 KB)
            ],
            'alt_text' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'image.required' => 'Vui lòng chọn ảnh để upload.',
            'image.image' => 'File upload phải là hình ảnh.',
            'image.mimes' => 'Chỉ chấp nhận các định dạng: jpeg, png, jpg, webp.',
            'image.max' => 'Dung lượng ảnh tối đa là 2MB.',
        ];
    }
}
