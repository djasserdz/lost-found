<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|comment query()
 * @property int $id
 * @property int $user_id
 * @property int $post_id
 * @property int|null $parent_id
 * @property string $body
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|comment whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|comment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|comment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|comment wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|comment whereUserId($value)
 * @property-read \App\Models\Post $post
 * @property-read \App\Models\User $user
 * @mixin \Eloquent
 */
class comment extends Model
{
    protected $table = 'comments';

    protected $fillable = [
        'user_id',
        'post_id',
        'parent_id',
        'body',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
    public function comments(): HasMany
    {
        return $this->hasMany(comment::class, 'parent_id');
    }
}
