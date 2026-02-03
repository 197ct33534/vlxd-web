<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageUrlRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'url' => 'required|url',
            'alt_text' => 'nullable|string|max:255',
        ];
    }
    
    public function messages()
    {
        return [
            'url.required' => 'Vui lòng nhập URL hình ảnh.',
            'url.url' => 'URL không hợp lệ.',
        ];
    }
}
