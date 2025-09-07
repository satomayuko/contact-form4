<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tel1' => trim((string) $this->input('tel1')),
            'tel2' => trim((string) $this->input('tel2')),
            'tel3' => trim((string) $this->input('tel3')),
        ]);
    }

    public function rules(): array
    {
        return [
            'last_name'  => ['required', 'string'],
            'first_name' => ['required', 'string'],
            'gender'     => ['required', Rule::in(['男性', '女性', 'その他'])],
            'email'      => ['required', 'email'],
            'address'    => ['required', 'string'],
            'building'   => ['nullable', 'string'],
            'type'       => ['required', 'exists:categories,id'],
            'content'    => ['required', 'string', 'max:120'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $tel1 = $this->input('tel1');
            $tel2 = $this->input('tel2');
            $tel3 = $this->input('tel3');

            if ($tel1 === '' || $tel2 === '' || $tel3 === '') {
                $validator->errors()->add('tel', '電話番号を入力してください');
                return;
            }

            if (!ctype_digit($tel1) || !ctype_digit($tel2) || !ctype_digit($tel3)) {
                $validator->errors()->add('tel', '電話番号は半角数字で入力してください');
                return;
            }

            if (
                strlen($tel1) < 1 || strlen($tel1) > 5 ||
                strlen($tel2) < 1 || strlen($tel2) > 5 ||
                strlen($tel3) < 1 || strlen($tel3) > 5
            ) {
                $validator->errors()->add('tel', '電話番号は各パート1〜5桁の数字で入力してください');
            }
        });
    }

    public function messages(): array
    {
        return [
            'last_name.required'  => '姓を入力してください',
            'first_name.required' => '名を入力してください',
            'gender.required'     => '性別を選択してください',
            'gender.in'           => '性別を選択してください',
            'email.required'      => 'メールアドレスを入力してください',
            'email.email'         => 'メールアドレスは「ユーザー名@ドメイン」形式で入力してください',
            'address.required'    => '住所を入力してください',
            'type.required'       => 'お問い合わせの種類を選択してください',
            'type.exists'         => 'お問い合わせの種類を正しく選択してください',
            'content.required'    => 'お問い合わせ内容を入力してください',
            'content.max'         => 'お問合せ内容は120文字以内で入力してください',
        ];
    }
}