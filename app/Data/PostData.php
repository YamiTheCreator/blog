<?php

namespace App\Data;

use App\Models\Post;
use Spatie\LaravelData\Data;

class PostData extends Data
{
    public function __construct(
        public string $id,
        public string $title,
        public string $text,
        public string $user_id,
        public ?UserData $user,
        public string $created_at,
        public string $updated_at,
    ) {}

    public static function fromModel(Post $post): self
    {
        return new self(
            id: $post->id,
            title: $post->title,
            text: $post->text,
            user_id: $post->user_id,
            user: $post->relationLoaded('user') ? UserData::fromModel($post->user) : null,
            created_at: $post->created_at->toISOString(),
            updated_at: $post->updated_at->toISOString(),
        );
    }
}
