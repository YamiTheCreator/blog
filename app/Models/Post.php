<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Screen\AsSource;

class Post extends Model
{
    use HasFactory, HasUuids, Filterable, AsSource;

    protected $fillable = [
        'title',
        'text',
        'user_id'
    ];

    protected $allowedFilters = [
        'title' => Like::class,
        'text' => Like::class,
        'created_at' => WhereDateStartEnd::class,
        'updated_at' => WhereDateStartEnd::class,
    ];

    protected $allowedSorts = [
        'title',
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

