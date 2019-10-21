<?php
namespace App\Models;

use Cog\Contracts\Love\Like\Models\Like as LikeContract;
use Cog\Laravel\Love\Like\Models\Like as CogLike;

/**
 * App\Models\Like
 *
 * @property int $id
 * @property string $likeable_type
 * @property int $likeable_id
 * @property int $user_id
 * @property string $type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Like $likeable
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Like newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Like newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Like query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Like whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Like whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Like whereLikeableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Like whereLikeableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Like whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Like whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Like whereUserId($value)
 * @mixin \Eloquent
 */
class Like extends CogLike implements LikeContract
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
