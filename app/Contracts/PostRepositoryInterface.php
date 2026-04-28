<?php

namespace App\Contracts;

use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface PostRepositoryInterface
{
    public function create(string $userId, array $data): Post;

    public function list(array $params, ?string $userId = null): Collection;

    public function getQuery(): Builder;

    public function findById(string $id): ?Post;

    public function update(string $id, array $data): bool;

    public function delete(string $id): bool;
}
