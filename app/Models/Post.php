<?php

namespace App\Models;

use App\Models\Scopes\PublishedScope;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy([PublishedScope::class])]
class Post extends Model
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the user that owns the Post
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
