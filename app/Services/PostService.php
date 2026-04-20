<?php

namespace App\Services;

use App\Models\Post;

class PostService
{
    public function create(array $data, int $userId): Post
    {
        return Post::create([...$data, 'user_id' => $userId]);
    }
}
