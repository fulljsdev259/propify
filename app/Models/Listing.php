<?php

namespace App\Models;

use App\Traits\HasComments;
use Cog\Contracts\Love\Likeable\Models\Likeable as LikeableContract;
use Cog\Laravel\Love\Likeable\Models\Traits\Likeable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use App\Traits\HasMediaTrait;

/**
 * App\Models\Listing
 *
 * @SWG\Definition (
 *      definition="Listing",
 *      required={"content", "contact"},
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
 *          property="status",
 *          description="status",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="visibility",
 *          description="visibility",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="content",
 *          description="content",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="price",
 *          description="price",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contact",
 *          description="contact",
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
 * @property int $user_id
 * @property int $type
 * @property int $status
 * @property int $visibility
 * @property string $title
 * @property string $content
 * @property string $contact
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property int|null $address_id
 * @property int|null $quarter_id
 * @property string|null $price
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $allComments
 * @property-read int|null $all_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Like[] $dislikes
 * @property-read int $dislikes_count
 * @property-read \Cog\Laravel\Love\LikeCounter\Models\LikeCounter $dislikesCounter
 * @property-read bool $disliked
 * @property-read bool $liked
 * @property-read int|null $likes_count
 * @property-read int $likes_diff_dislikes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Like[] $likes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Like[] $likesAndDislikes
 * @property-read int|null $likes_and_dislikes_count
 * @property-read \Cog\Laravel\Love\LikeCounter\Models\LikeCounter $likesCounter
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Listing onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing orderByDislikesCount($direction = 'desc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing orderByLikesCount($direction = 'desc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing whereContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing whereDislikedBy($userId = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing whereLikedBy($userId = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing whereQuarterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Listing whereVisibility($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Listing withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Listing withoutTrashed()
 * @mixin \Eloquent
 */
class Listing extends AuditableModel implements HasMedia, LikeableContract, Auditable
{
    use SoftDeletes;
    use HasMediaTrait;
    use Likeable;
    use HasComments;
    use \OwenIt\Auditing\Auditable;

    public $table = 'listings';

    const TypeSell = 1;
    const TypeLend = 2;
    const TypeService = 3;
    const TypeGiveAway = 4;

    const StatusUnpublished = 1;
    const StatusPublished = 2;

    const VisibilityAddress = 1;
    const VisibilityQuarter = 2;
    const VisibilityAll = 3;

    const Type = [
        self::TypeSell => 'sell',
        self::TypeLend => 'lend',
        self::TypeService => 'service',
        self::TypeGiveAway => 'giveaway',
    ];
    const Status = [
        self::StatusUnpublished => 'unpublished',
        self::StatusPublished => 'published',
    ];
    const Visibility = [
        self::VisibilityAddress => 'address',
        self::VisibilityQuarter => 'quarter',
        self::VisibilityAll => 'all',
    ];

    protected $dates = ['deleted_at', 'published_at'];

    const Fillable = [
        'user_id',
        'type',
        'status',
        'visibility',
        'content',
        'contact',
        'title',
        'price',
    ];
    public $fillable = self::Fillable;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'type' => 'integer',
        'status' => 'integer',
        'visibility' => 'integer',
        'content' => 'string',
        'contact' => 'string',
        'title' => 'string',
        'price' => 'string',
    ];

    protected $permittedExtensions = [
        'jpg',
        'png',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static function rules()
    {
        return [
            'content' => 'required|string',
            'contact' => 'required|string',
            'title' => 'required|string',
            'price' => 'nullable|string|numeric',
            'visibility' => ['required', Rule::in(array_keys(self::Visibility))],
            'type' => ['required', Rule::in(array_keys(self::Type))],
        ];
    }

    /**
     * Publish validation rules
     *
     * @var array
     */
    public static function publishRules()
    {
        return [
            'status' => ['required', Rule::in(array_keys(self::Status))]
        ];
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
}
