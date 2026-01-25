<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductCreateRequest extends FormRequest
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
            'description' => ['required', 'max:255'],
            'image' => ['required','image', 'mimes:png,jpeg'],
            'category_ids'   => ['required', 'array'],
            'category_ids.*' => ['exists:categories,id'],
            'brand' => ['nullable', 'string', 'max:20'],
            'condition_id' => ['required', 'exists:conditions,id'],
            'price' => ['required','numeric', 'min:0']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '商品名を入力してください',
            'description.required' => '商品の説明を入力してください',
            'image.required' => '画像ファイルを選択してください',
            'image.mimes' => '画像はpngまたはjpeg形式でアップロードしてください。',
            'image.image' => '画像ファイルを選択してください',
            'category_ids.required' => 'カテゴリーを選択してください',
            'brand.max' => '20文字以内で入力してください',
            'condition_id.required' => '商品の状態を選択してください',
            'price.required' => '販売価格を入力してください'
        ];
    }
}
