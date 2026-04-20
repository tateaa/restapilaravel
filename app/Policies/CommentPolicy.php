<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    // Anyone bisa view comment
    public function view(User $user, Comment $comment): bool
    {
        return true;
    }

    // Admin bisa edit semua; editor hanya comment miliknya
    public function update(User $user, Comment $comment): bool
    {
        return $user->hasRole('admin')
            || ($user->hasRole('editor') && $user->id === $comment->user_id);
    }

    // Hanya admin yang bisa hapus
    public function delete(User $user, Comment $comment): bool
    {
        return $user->hasRole('admin');
    }
}
