<?php

namespace App\Repositories;

use App\Contracts\PostRepositoryInterface;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PostRepository implements PostRepositoryInterface
{
    public function create(string $userId, array $data): Post
    {
        return Post::query()->create([
            'title' => $data['title'],
            'text' => $data['text'],
            'user_id' => $userId,
        ]);
    }

    public function list(array $params, ?string $userId = null): Collection
    {
        $query = Post::query();

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if (!empty($params['title'])) {
            $query->where('title', 'like', '%' . $params['title'] . '%');
        }

        if (!empty($params['date_from'])) {
            $query->whereDate('created_at', '>=', $params['date_from']);
        }
        if (!empty($params['date_to'])) {
            $query->whereDate('created_at', '<=', $params['date_to']);
        }

        $sortField = $params['sort_by'] ?? 'created_at'; // по умолчанию дата
        $sortOrder = $params['sort_order'] ?? 'desc';    // по умолчанию новые сверху

        if (in_array($sortField, ['created_at', 'title'])) {
            $query->orderBy($sortField, $sortOrder);
        }

        $limit = (int)($params['limit'] ?? 10);
        $offset = (int)($params['offset'] ?? 0);

        return $query->limit($limit)->offset($offset)->get();
    }

    public function getQuery(): Builder
    {
        return Post::query()->with('user');
    }

    public function findById(string $id): ?Post
    {
        return Post::query()->findOrFail($id);
    }

    public function update(string $id, array $data): bool
    {
        $post = $this->findById($id);
        return $post->update($data);
    }

    public function delete(string $id): bool
    {
        return Post::query()->where('id', $id)->delete();
    }
}
