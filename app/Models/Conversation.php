<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasComments;
use App\Notifications\RequestInternalComment;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * App\Models\Conversation
 *
 * @SWG\Definition (
 *      definition="Conversation",
 *      required={"conversation"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 * @property int $id
 * @property string $conversationable_type
 * @property int $conversationable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $allComments
 * @property-read int|null $all_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation ofLoggedInUser()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereConversationableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereConversationableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Conversation extends Model
{
    use HasComments;
    protected $fillable = [];

    /**
     * Validation rules
     *
     * @var array
     */
    public static function rules()
    {
        return [];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function scopeOfLoggedInUser($query)
    {
        return $query->whereHas('users', function($q) {
            return $q->where('id', \Auth::id());
        });
    }

    public function notifyComment(Comment $comment)
    {
        $cType = get_morph_type_of(Request::class);
        if ($this->conversationable_type != $cType) {
            return;
        }

        $sr = Request::findOrFail($this->conversationable_id);
        // If this is a service request conversation
        foreach ($this->users as $user) {
            if ($user->id != \Auth::id()) {
                $user->notify(new RequestInternalComment($sr, $comment, $user));
            }
        }
    }
}
