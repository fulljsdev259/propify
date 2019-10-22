<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use BeyondCode\Comments\Comment as Comment_;

/**
 * App\Models\Comment
 *
 * @SWG\Definition (
 *      definition="Comment",
 *      required={"comment"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="user_id",
 *          description="user_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          description="type",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment body",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 * @property int $id
 * @property string $commentable_type
 * @property int $commentable_id
 * @property int|null $parent_id
 * @property string $comment
 * @property bool $is_approved
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $children
 * @property-read int|null $children_count
 * @property-read \App\Models\Comment $childrenCountRelation
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $commentable
 * @property-read \App\Models\User|null $commentator
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\BeyondCode\Comments\Comment approved()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereIsApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Comment whereUserId($value)
 * @mixin \Eloquent
 */
class Comment extends Comment_
{
    protected $fillable = [
        'comment',
        'user_id',
        'is_approved',
        'parent_id',
    ];

    /**
     * Validation rules
     *
     * @var array
     * @TODO find a way to validate parent_id so that the parent and the child
     * both have the same commentable type and commentable id
     */
    public static function rules()
    {
        return [
            'comment' => 'required|string',
            'parent_id' => [
                'exists:comments,id',
                function ($attribute, $value, $fail) {
                    $c = Comment::find($value);
                    if ($c && !is_null($c->parent_id)) {
                        $fail('parent_id is invalid.');
                    }
                },
            ],
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function children()
    {
        return $this->hasMany(Comment::class, 'parent_id', 'id');
    }

    public function getChildrenCountAttribute()
    {
        if (!$this->relationLoaded('childrenCountRelation')) {
            return 0;
        }
        $related = $this->getRelation('childrenCountRelation');
        return ($related) ? (int) $related->aggregate : 0;
    }

    public function childrenCountRelation()
    {
        return $this->hasOne(Comment::class, 'parent_id')
        ->selectRaw('parent_id, count(*) as aggregate')
            ->groupBy('parent_id');
    }

    // relationExists returns whether the relation named $key exists, is loaded
    // and is not null
    public function relationExists($key)
    {
        return parent::relationLoaded($key) && isset($this->$key);
    }
}
