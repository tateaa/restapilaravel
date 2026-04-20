<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    // Admin bisa buat post siapa saja; editor bisa buat milik sendiri
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'editor']);
    }

    // Admin bisa edit semua; editor hanya post miliknya
    public function update(User $user, Post $post): bool
    {
        return $user->hasRole('admin')
            || ($user->hasRole('editor') && $user->id === $post->user_id);
    }

    // Hanya admin yang bisa hapus
    public function delete(User $user, Post $post): bool
    {
        return $user->hasRole('admin');
    }
}
