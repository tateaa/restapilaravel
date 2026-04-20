<?php

namespace App\Http\Requests;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Post::class);
    }

    public function rules(): array
    {
        return [
            'title'   => ['required', 'string', 'max:100'],
            'status'  => ['required', Rule::enum(PostStatus::class)],
            'content' => ['required', 'string', 'min:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'  => 'Judul post wajib diisi.',
            'content.min'     => 'Konten minimal 10 karakter.',
            'status.required' => 'Status post wajib dipilih.',
        ];
    }
}
