<?php

namespace App\Http\Requests;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        $post = $this->route('post');
        return $this->user()->can('update', $post);
    }

    public function rules(): array
    {
        return [
            'title'   => ['sometimes', 'string', 'max:100'],
            'status'  => ['sometimes', Rule::enum(PostStatus::class)],
            'content' => ['sometimes', 'string', 'min:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.min' => 'Konten minimal 10 karakter.',
        ];
    }
}
