<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin', 'editor']);
    }

    public function rules(): array
    {
        return [
            'comment' => ['required', 'string', 'max:250'],
        ];
    }

    public function messages(): array
    {
        return [
            'comment.required' => 'Komentar wajib diisi.',
            'comment.max'      => 'Komentar maksimal 250 karakter.',
        ];
    }
}
