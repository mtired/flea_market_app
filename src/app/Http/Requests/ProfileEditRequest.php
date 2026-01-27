<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileEditRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:20'],
            'postal_code' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/', 'max:8'],
            'address' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable','image', 'mimes:png,jpeg']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '名前を入力してください',
            'postal_code.required' => '郵便番号を入力してください',
            'address.required' => '住所を入力してください',
            'building.max' => '255文字以内で入力してください',
            'postcode.regex' => '郵便番号は「123-4567」の形式で入力してください',
        ];
    }
}
