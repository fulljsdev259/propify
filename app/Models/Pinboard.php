<?php

namespace App\Models;

use App\Traits\HasComments;
use Cog\Contracts\Love\Likeable\Models\Likeable as LikeableContract;
use Cog\Laravel\Love\Likeable\Models\Traits\Likeable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use App\Traits\HasMediaTrait;

/**
 * App\Models\Pinboard
 *
 * @SWG\Definition (
 *      definition="Pinboard",
 *      required={"content"},
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
 *          property="sub_type",
 *          description="sub_type",
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
 *          property="category",
 *          description="category",
 *          type="int32"
 *      ),
 *      @SWG\Property(
 *          property="content",
 *          description="content",
 *          type="string"
 *      ),
 *     @SWG\Property(
 *          property="is_execution_time",
 *          description="is_execution_time",
 *          type="integer"
 *      ),
 *     @SWG\Property(
 *          property="execution_period",
 *          description="execution_period",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="execution_start",
 *          description="execution_start",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="execution_end",
 *          description="execution_end",
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
 * @property int|null $sub_type
 * @property int $status
 * @property int $visibility
 * @property bool|null $category_image
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property bool $announcement
 * @property int|null $category
 * @property bool $is_execution_time
 * @property int $execution_period
 * @property \Illuminate\Support\Carbon|null $execution_start
 * @property \Illuminate\Support\Carbon|null $execution_end
 * @property string|null $title
 * @property bool $notify_email
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $allComments
 * @property-read int|null $all_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AnnouncementEmailReceptionist[] $announcement_email_receptionists
 * @property-read int|null $announcement_email_receptionists_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Building[] $buildings
 * @property-read int|null $buildings_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Like[] $dislikes
 * @property-read int $dislikes_count
 * @property-read \Cog\Laravel\Love\LikeCounter\Models\LikeCounter $dislikesCounter
 * @property-read mixed $buildings_str
 * @property-read mixed $category_str
 * @property-read bool $disliked
 * @property-read mixed $execution_end_str
 * @property-read mixed $execution_start_str
 * @property-read bool $liked
 * @property-read int|null $likes_count
 * @property-read int $likes_diff_dislikes_count
 * @property-read mixed $providers_str
 * @property-read mixed $quarters_str
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Like[] $likes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Like[] $likesAndDislikes
 * @property-read int|null $likes_and_dislikes_count
 * @property-read \Cog\Laravel\Love\LikeCounter\Models\LikeCounter $likesCounter
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ServiceProvider[] $providers
 * @property-read int|null $providers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Quarter[] $quarters
 * @property-read int|null $quarters_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PinboardView[] $views
 * @property-read int|null $views_count
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Pinboard onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard orderByDislikesCount($direction = 'desc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard orderByLikesCount($direction = 'desc')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereAnnouncement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereCategoryImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereDislikedBy($userId = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereExecutionEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereExecutionPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereExecutionStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereIsExecutionTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereLikedBy($userId = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereNotifyEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereSubType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Pinboard whereVisibility($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Pinboard withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Pinboard withoutTrashed()
 * @mixin \Eloquent
 */
class Pinboard extends AuditableModel implements HasMedia, LikeableContract
{
    use SoftDeletes;
    use HasMediaTrait;
    use Likeable;
    use HasComments;

    public $table = 'pinboard';

    const TypePost = 1;
    const TypeNewNeighbour = 2;
    const TypeAnnouncement = 3;
    const TypeArticle = 4;

    const SubTypeImportant = 1;
    const SubTypeCritical = 2;
    const SubTypeMaintenance = 3;

    const StatusNew = 1;
    const StatusPublished = 2;
    const StatusUnpublished = 3;
    const StatusNotApproved = 4;

    const VisibilityAddress = 1;
    const VisibilityQuarter = 2;
    const VisibilityAll = 3;

    const CategoryGeneral = 1;
    const CategoryMaintenance = 2;
    const CategoryElectricity = 3;
    const CategoryHeating = 4;
    const CategorySanitary = 5;

    const ExecutionPeriodSingleDay = 1;
    const ExecutionPeriodManyDay = 2;

    const Type = [
        self::TypePost => 'post',
        self::TypeNewNeighbour => 'new_neighbour',
        self::TypeAnnouncement => 'announcement',
        self::TypeArticle => 'article',
    ];
    const SubType = [
        self::TypeAnnouncement => [
            self::SubTypeImportant => 'important',
            self::SubTypeCritical => 'critical',
            self::SubTypeMaintenance => 'maintenance',
        ]
    ];
    const Status = [
        self::StatusNew => 'new',
        self::StatusPublished => 'published',
        self::StatusUnpublished => 'unpublished',
        self::StatusNotApproved => 'not_approved',
    ];
    const Visibility = [
        self::VisibilityAddress => 'address',
        self::VisibilityQuarter => 'quarter',
        self::VisibilityAll => 'all',
    ];
    const Category = [
        self::CategoryGeneral => 'general',
        self::CategoryMaintenance => 'maintenance',
        self::CategoryElectricity => 'electricity',
        self::CategoryHeating => 'heating',
        self::CategorySanitary => 'sanitary',
    ];

    const ExecutionPeriod = [
        self::ExecutionPeriodSingleDay => 'single_day',
        self::ExecutionPeriodManyDay => 'many_day',
    ];

    const Fillable = [
        'user_id',
        'type',
        'sub_type',
        'content',
        'visibility',
        'category',
        'quarter_id',
        'announcement',
        'execution_start',
        'execution_end',
        'title',
        'notify_email',
        'category_image',
        'is_execution_time',
        'execution_period',
        'published_at',
        'status'
    ];

    public $fillable = self::Fillable;

    protected $dates = [
        'deleted_at',
        'published_at',
        'execution_start',
        'execution_end'
    ];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'type' => 'integer',
        'sub_type' => 'integer',
        'status' => 'integer',
        'visibility' => 'integer',
        'execution_period' => 'integer',
        'content' => 'string',
        'announcement' => 'boolean',
        'notify_email' => 'boolean',
        'category_image' => 'boolean',
        'is_execution_time' => 'boolean',
    ];

    const templateMap = [
        'title' => 'pinboard.title',
        'content' => 'pinboard.content',
        'providers' => 'pinboard.providersList',
        'category' => 'pinboard.categoryStr',
        'execution_start' => 'pinboard.execution_start',
        'execution_end' => 'pinboard.execution_end',
        'autologinUrl' => 'user.autologinUrl',
    ];

    /**
     * @var array
     */
    protected $permittedExtensions = [
        'jpg',
        'png',
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'jpeg'
    ];

    /**
     * @var array
     */
    protected $auditEvents = [
        AuditableModel::EventCreated,
        AuditableModel::EventUpdated,
        AuditableModel::EventDeleted,

        AuditableModel::EventProviderAssigned => 'getAttachedEventAttributes',
        AuditableModel::EventProviderUnassigned => 'getDetachedEventAttributes',

        AuditableModel::EventQuarterAssigned => 'getAttachedEventAttributes',
        AuditableModel::EventQuarterUnassigned => 'getDetachedEventAttributes',

        AuditableModel::EventBuildingAssigned => 'getAttachedEventAttributes',
        AuditableModel::EventBuildingUnassigned => 'getDetachedEventAttributes',
    ];

    /**
     * Validation rules
     *
     * @return array
     * @var array
     */
    public static function rules()
    {
        $categories = array_keys(self::Category);
        $categories[] = null;
        $settings = Settings::first();
        $visibilities = self::Visibility;
        if (!$settings->quarter_enable) {
            unset($visibilities[self::VisibilityQuarter]);
        }
        return [
            'content' => 'required',
            'visibility' => ['nullable', Rule::in(array_keys($visibilities))],
            'category' => [Rule::in($categories)],
            'announcement' => function ($attribute, $value, $fail) {
                if ($value && !\Auth::user()->can('announcement-pinboard')) {
                    $fail($attribute.' must be false.');
                }
            },
            'pinned' => function ($attribute, $value, $fail) {      // @TODO delete
                if ($value && !\Auth::user()->can('announcement-pinboard')) {
                    $fail($attribute.' must be false.');
                }
            },
            'execution_start' => 'nullable|date',
            'execution_end' => 'nullable|date|after_or_equal:execution_start',
        ];
    }

    /**
     * Publish validation rules
     *
     * @return array
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
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function buildings()
    {
        return $this->belongsToMany(Building::class);
    }

    public function quarters()
    {
        return $this->belongsToMany(Quarter::class, 'pinboard_quarter');
    }

    public function providers()
    {
        return $this->belongsToMany(ServiceProvider::class);
    }

    public function views()
    {
        return $this->hasMany(PinboardView::class);
    }

    public function announcement_email_receptionists()
    {
        return $this->hasMany(AnnouncementEmailReceptionist::class);
    }

    public function getExecutionStartStrAttribute()
    {
        if (!$this->execution_start) {
            return '-';
        }
        return $this->execution_start->format('d.m.Y H:i');
    }
    public function getExecutionEndStrAttribute()
    {
        if (!$this->execution_end) {
            return '-';
        }
        return $this->execution_end->format('d.m.Y H:i');
    }

    public function getProvidersStrAttribute()
    {
        if (!count($this->providers)) {
            return '-';
        }
        $pNames = $this->providers->map(function($el) {
            return $el->name;
        })->toArray();

        return implode(', ', $pNames);
    }

    public function getBuildingsStrAttribute()
    {
        if (!count($this->buildings)) {
            return '-';
        }
        $pNames = $this->buildings->map(function($el) {
            return $el->name;
        })->toArray();

        return implode(', ', $pNames);
    }

    public function getQuartersStrAttribute()
    {
        if (!count($this->quarters)) {
            return '-';
        }
        $pNames = $this->quarters->map(function($el) {
            return $el->name;
        })->toArray();

        return implode(', ', $pNames);
    }

    public function getCategoryStrAttribute()
    {
        if (!$this->category) {
            return '-';
        }

        return self::Category[$this->category];
    }

    public function incrementViews(int $userID)
    {
        $uv = PinboardView::where('pinboard_id', $this->id)
            ->where('user_id', $userID)
            ->first();
        if (!$uv) {
            $uv = new PinboardView();
            $uv->user_id = $userID;
            $uv->pinboard_id = $this->id;
        }
        $uv->views += 1;
        $uv->save();
        return $uv;
    }

    /**
     * @param $key
     * @param $value
     * @param null $audit
     * @param bool $isSingle
     */
    public function addDataInAudit($key, $value, $audit = null, $isSingle = true)
    {
        if ('notifications' == $key) {
            $_value = [];
            foreach ($value as $morph => $data) {
                if ($data->pluck('resident.id')->isEmpty()) {
                    continue;
                }
                $_value[$morph] = [
                    'resident_ids' => $data->pluck('resident.id')->all(),
                    'failed_resident_ids' => []
                ];
            }
        } else {
            $_value = $value;
        }

        parent::addDataInAudit($key, $_value, $audit, $isSingle); // TODO: Change the autogenerated stub
    }
}
