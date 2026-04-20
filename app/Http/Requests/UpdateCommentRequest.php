<?php

namespace App\Http\Requests;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $comment = $this->route('comment');
        return $this->user()->can('update', $comment);
    }

    public function rules(): array
    {
        return [
            'comment' => ['sometimes', 'string', 'max:250'],
        ];
    }

    public function messages(): array
    {
        return [
            'comment.max' => 'Komentar maksimal 250 karakter.',
        ];
    }
}
