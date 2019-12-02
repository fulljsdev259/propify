<?php

namespace App\Models;

use App\Traits\HasComments;
use App\Traits\UniqueIDFormat;
use Chelout\RelationshipEvents\Concerns\HasMorphedByManyEvents;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use App\Traits\HasMediaTrait;
use PDF;
use Storage;

/**
 * App\Models\Request
 *
 * @SWG\Definition (
 *      definition="Request",
 *      required={"description", "status", "priority", "due_date"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="category_id",
 *          description="category_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="creator_user_id",
 *          description="creator_user_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="resident_id",
 *          description="resident_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="relation_id",
 *          description="relation_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *     @SWG\Property(
 *          property="reminder_user_ids",
 *          description="reminder_user_ids",
 *          type="integer",
 *          format="int32"
 *      ),
 *     @SWG\Property(
 *          property="days_left_due_date",
 *          description="days_left_due_date",
 *          type="integer",
 *          format="int32"
 *      ),
 *     @SWG\Property(
 *          property="active_reminder",
 *          description="active_reminder",
 *          type="boolean",
 *      ),
 *      @SWG\Property(
 *          property="title",
 *          description="title",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="status",
 *          description="status",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="priority",
 *          description="priority",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="due_date",
 *          description="due_date",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="solved_date",
 *          description="solved_date",
 *          type="string",
 *          format="date"
 *      ),
 *     @SWG\Property(
 *          property="visibility",
 *          description="visibility",
 *          type="int32"
 *      ),
 *     @SWG\Property(
 *          property="percentage",
 *          description="percentage",
 *          type="float"
 *      ),
 *     @SWG\Property(
 *          property="is_public",
 *          description="is_public",
 *          type="boolean"
 *      ),
 *     @SWG\Property(
 *          property="notify_email",
 *          description="notify_email",
 *          type="boolean"
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
 * @property int|null $creator_user_id
 * @property int $category_id
 * @property int $resident_id
 * @property int|null $relation_id
 * @property float|null $percentage
 * @property string $request_format
 * @property string $title
 * @property string $description
 * @property int $status
 * @property int $priority
 * @property int $internal_priority
 * @property int|null $location
 * @property int|null $payer
 * @property string|null $component
 * @property int|null $capture_phase
 * @property int|null $room
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property \Illuminate\Support\Carbon|null $solved_date
 * @property \Illuminate\Support\Carbon|null $reactivation_date
 * @property int $resolution_time in seconds
 * @property bool $active_reminder
 * @property int $days_left_due_date
 * @property int|null $reminder_user_ids
 * @property array $sent_reminder_user_ids
 * @property bool $is_public
 * @property bool $notify_email
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $visibility
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $allComments
 * @property-read int|null $all_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RequestAssignee[] $assignees
 * @property-read int|null $assignees_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\Relation|null $relation
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Conversation[] $conversations
 * @property-read int|null $conversations_count
 * @property-read \App\Models\User|null $creator
 * @property-read mixed $all_people
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PropertyManager[] $managers
 * @property-read int|null $managers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ServiceProvider[] $providers
 * @property-read int|null $providers_count
 * @property-read \App\Models\Resident $resident
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read \App\Models\Unit $unit
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Request onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereActiveReminder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereCapturePhase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereComponent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereCreatorUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereDaysLeftDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereInternalPriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereNotifyEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request wherePayer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereReactivationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereReminderUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereRequestFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereResidentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereResolutionTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereRoom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereSentReminderUserIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereSolvedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereVisibility($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Request withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Request withoutTrashed()
 * @mixin \Eloquent
 */
class Request extends AuditableModel implements HasMedia
{
    use SoftDeletes;
    use HasMediaTrait;
    use HasComments;
    use HasMorphedByManyEvents;
    use UniqueIDFormat;

    public $table = 'requests';

    const StatusNew = 1;
    const StatusPending = 2;
    const StatusInProcessing = 3;
    const StatusDone = 4;
    const StatusArchived = 5;
    const StatusWarrantyClaim = 6;
//    const StatusAssigned = 3;
//    const StatusReactivated = 5;

    const VisibilityResident = 1;
    const VisibilityBuilding = 2;
    const VisibilityQuarter = 3;

    const PendingStatuses = [
        self::StatusNew,
        self::StatusInProcessing,
//        self::StatusAssigned,
//        self::StatusReactivated,
    ];

    const SolvedStatuses = [
        self::StatusDone,
        self::StatusArchived,
    ];
//@TODO CORRECT status related
    const Status = [
        self::StatusNew => 'new',
        self::StatusInProcessing => 'in_processing',
        self::StatusPending => 'pending',
//        self::StatusAssigned => 'assigned',
        self::StatusDone => 'done',
        self::StatusWarrantyClaim => 'warranty_claim',
//        self::StatusReactivated => 'reactivated',
        self::StatusArchived => 'archived',
    ];

    const StatusColorCode = [
        self::StatusNew => '#c0772c',
        self::StatusPending => '#c8a331',
        self::StatusInProcessing => '#317085',
        self::StatusDone => '#878810',
        self::StatusArchived => '#9e9fa0',
        self::StatusWarrantyClaim => '#897e82',
    ];

    const StatusByResident = [
        self::StatusNew => [self::StatusDone],
//        self::StatusAssigned => [self::StatusDone, self::StatusArchived],
        self::StatusInProcessing => [self::StatusDone, self::StatusArchived],
//        self::StatusDone => [self::StatusReactivated],
//        self::StatusReactivated => [self::StatusDone],
        self::StatusArchived => [],
    ];

    const StatusByService = [
        self::StatusNew => [],
        self::StatusInProcessing => [self::StatusDone],
//        self::StatusAssigned => [self::StatusDone],
//        self::StatusDone => [self::StatusReactivated],
//        self::StatusReactivated => [self::StatusDone],
        self::StatusArchived => [],
    ];

    const StatusByAgent = [
//        self::StatusNew => [self::StatusAssigned],
//        self::StatusAssigned => [self::StatusInProcessing, self::StatusDone, self::StatusArchived],
        self::StatusInProcessing => [self::StatusDone, self::StatusArchived],
        self::StatusDone => [/*self::StatusReactivated,*/ self::StatusArchived],
//        self::StatusReactivated => [self::StatusDone, self::StatusArchived],
//        self::StatusArchived => [self::StatusReactivated],
    ];

    const Visibility = [
        self::VisibilityResident => 'resident',
        self::VisibilityBuilding => 'building',
        self::VisibilityQuarter => 'quarter',
    ];

    const Priority = [
        1 => 'low',
        2 => 'normal',
        3 => 'urgent',
    ];

    const ActionFix = 1;
    const ActionLeave = 2;
    const ActionWait = 3;

    const Action = [
        self::ActionFix => 'fix',
        self::ActionLeave => 'leave',
        self::ActionWait => 'wait',
    ];

    const CostImpactHouseOwner = 1;
    const CostImpactResident = 2;
    const CostImpactSharedCosts = 3;

    const CostImpact = [
        self::CostImpactHouseOwner => 'house_owner',
        self::CostImpactResident => 'resident',
        self::CostImpactSharedCosts => 'shared_costs'
    ];

    const LocationHouseEntrance = 1;
    const LocationStaircase = 2;
    const LocationElevator = 3;
    const LocationUndergroundCarPark = 4;
    const LocationWashingDrying = 5;
    const LocationTechnologyHeating = 6;
    const LocationTechnologyElectro = 7;
    const LocationFacade = 8;
    const LocationRoof = 9;
    const LocationOther = 10;
    const Location = [
        self::LocationHouseEntrance => 'house_entrance',
        self::LocationStaircase => 'staircase',
        self::LocationElevator => 'elevator',
        self::LocationUndergroundCarPark => 'car_park',
        self::LocationWashingDrying => 'washing',
        self::LocationTechnologyHeating => 'heating',
        self::LocationTechnologyElectro => 'electro',
        self::LocationFacade => 'facade',
        self::LocationRoof => 'roof',
        self::LocationOther => 'other',
    ];


    const RoomBathroomWC = 1;
    const RoomShowerWC = 2;
    const RoomEntrance = 3;
    const RoomPassage = 4;
    const RoomBasement = 5;
    const RoomKitchen = 6;
    const RoomReduite = 7;
    const RoomHabitation = 8;
    const RoomRoom1 = 9;
    const RoomRoom2 = 10;
    const RoomRoom3 = 11;
    const RoomRoom4 = 12;
    const RoomAll = 13;
    const RoomOther = 14;
    
    const Room = [
        self::RoomBathroomWC => 'bath',
        self::RoomShowerWC => 'shower',
        self::RoomEntrance => 'entrance',
        self::RoomPassage => 'passage',
        self::RoomBasement => 'basement',
        self::RoomKitchen => 'kitchen',
        self::RoomReduite => 'storeroom',
        self::RoomHabitation => 'habitation',
        self::RoomRoom1 => 'room1',
        self::RoomRoom2 => 'room2',
        self::RoomRoom3 => 'room3',
        self::RoomRoom4 => 'room4',
        self::RoomAll => 'all',
        self::RoomOther => 'other',
    ];

    const CapturePhaseOther = 1;
    const CapturePhaseConstructionPhase = 2;
    const CapturePhaseShellAcceptance = 3;
    const CapturePhasePreliminaryAcceptance = 4;
    const CapturePhaseAcceptanceOfWork = 5;
    const CapturePhaseSurrender = 6;
    const CapturePhaseAcceptance = 7;
    
    const CapturePhase = [
        self::CapturePhaseOther => 'other',
        self::CapturePhaseConstructionPhase => 'construction',
        self::CapturePhaseShellAcceptance => 'shell',
        self::CapturePhasePreliminaryAcceptance => 'preliminary',
        self::CapturePhaseAcceptanceOfWork => 'work',
        self::CapturePhaseSurrender => 'surrender',
        self::CapturePhaseAcceptance => 'inspection',
    ];

    const CategoryGeneral = 1;
    const CategoryMalfunction = 2;
    const CategoryDeficiency = 3;
    const CategoryOpenIssue = 4;

    const Category = [
        self::CategoryGeneral => 'general',
        self::CategoryMalfunction => 'malfunction',
        self::CategoryDeficiency => 'deficiency',
        self::CategoryOpenIssue => 'open_issue',
    ];

    const SubCategoryInsideOfApartment = 5;
    const SubCategoryOutsideOfApartment = 6;

    const SubCategory = [
        self::SubCategoryInsideOfApartment => 'inside_of_apartment',
        self::SubCategoryOutsideOfApartment => 'outside_of_apartment',
    ];

    const RoomAttr = 'room';
    const LocationAttr = 'location';
    const CapturePhaseAttr = 'capture_phase';
    const ComponentAttr = 'component';
    const ActionAttr = 'action';
    const CostImpactAttr = 'cost_impact';
    const SubQualificationCategoryAttr = 'qualification_category';
    const SubCategories = 'sub_categories';
    const Attributes = 'attributes';

    const CategoryAttributes = [
        self::CategoryGeneral => [
        ],
        self::CategoryMalfunction => [
            self::CostImpactAttr,
            self::ActionAttr,
            self::CapturePhaseAttr,
            self::ComponentAttr,
        ],
        self::CategoryDeficiency => [
            self::CostImpactAttr,
            self::ActionAttr,
            self::CapturePhaseAttr,
            self::ComponentAttr,
            self::SubQualificationCategoryAttr,
        ],
        self::CategoryOpenIssue => [
            self::CostImpactAttr,
            self::ActionAttr,
            self::CapturePhaseAttr,
            self::ComponentAttr,
        ],
    ];

    const SubCategoryAttributes = [
        self::SubCategoryInsideOfApartment => [
            self::CostImpactAttr,
            self::ActionAttr,
	        self::CapturePhaseAttr,
	        self::ComponentAttr,
	        self::RoomAttr,
        ],
        self::SubCategoryOutsideOfApartment => [
            self::CostImpactAttr,
            self::ActionAttr,
	        self::CapturePhaseAttr,
	        self::LocationAttr,
            self::ComponentAttr,

        ],
    ];

    const CategorySubCategory = [
        self::CategoryGeneral => [
            self::SubCategoryInsideOfApartment,
            self::SubCategoryOutsideOfApartment,
        ],
        self::CategoryMalfunction => [
            self::SubCategoryInsideOfApartment,
            self::SubCategoryOutsideOfApartment,
        ],
        self::CategoryDeficiency => [
            self::SubCategoryInsideOfApartment,
            self::SubCategoryOutsideOfApartment,
        ],
        self::CategoryOpenIssue => [
            self::SubCategoryInsideOfApartment,
            self::SubCategoryOutsideOfApartment,
        ],
    ];

    const QualificationCategoryNormalWear = 1;
    const QualificationCategoryDeficiency = 2;
    const QualificationCategoryReCleaning = 3;
    const QualificationCategoryNonExistent = 4;
    const QualificationCategoryOkay = 5;

    const QualificationCategory = [
        self::QualificationCategoryNormalWear => 'normal_wear',
        self::QualificationCategoryDeficiency => 'deficiency',
        self::QualificationCategoryReCleaning => 're_cleaning',
        self::QualificationCategoryNonExistent => 'non_existent',
        self::QualificationCategoryOkay => 'okay',
    ];

    const Fillable = [
        'creator_user_id',
        'reminder_user_ids',
        'category_id',
        'sub_category_id',
        'qualification_category',
        'subject_id',
        'resident_id',
        'relation_id',
        'title',
        'description',
        'status',
//        'priority',
//        'internal_priority',
        'due_date',
        'solved_date',
        'action',
        'visibility',
        'request_format',
        'room',
        'capture_phase',
        'component',
        'location',
        'reactivation_date',
        'resolution_time',
        'days_left_due_date',
        'active_reminder',
        'is_public',
        'notify_email',
        'percentage',
        'cost_impact',
    ];

    public $fillable = self::Fillable;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $formatLength = 3;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'category_id' => 'integer',
        'sub_category_id' => 'integer',
        'qualification_category' => 'integer',
        'creator_user_id' => 'integer',
        'reminder_user_ids' => 'array',
        'resident_id' => 'integer',
        'relation_id' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'status' => 'integer',
//        'priority' => 'integer',
//        'internal_priority' => 'integer',
        'due_date' => 'date',
        'solved_date' => 'datetime',
        'action' => 'integer',
        'visibility' => 'integer',
        'sent_reminder_user_ids' => 'array',
        'request_format' => 'string',
        'room' => 'integer',
        'capture_phase' => 'integer',
        'component' => 'string',
        'location' => 'integer',
        'reactivation_date' => 'datetime',
        'resolution_time' => 'integer',
        'days_left_due_date' => 'integer',
        'active_reminder' => 'boolean',
        'is_public' => 'boolean',
        'notify_email' => 'boolean',
        'percentage' => 'float',
        'cost_impact' => 'integer',
    ];


    const templateMap = [
        'title' => 'request.title',
        'description' => 'request.description',
//        'priority' => 'request.priorityStr',
        'autologinUrl' => '',
        'resident' => '',
        'category' => '',
        'unit' => '',
        'building' => '',
    ];

    /**
     * @var array
     */
    protected $permittedExtensions = [
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'png',
        'jpg',
        'jpeg'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rulesPut = [
    ];


    /**
     * @var array
     */
    protected $auditEvents = [
        AuditableModel::EventCreated,
        AuditableModel::EventUpdated,
        AuditableModel::EventDeleted,
        AuditableModel::EventUserAssigned => 'getAttachedEventAttributes',
        AuditableModel::EventManagerAssigned => 'getAttachedEventAttributes',
        AuditableModel::EventProviderAssigned => 'getAttachedEventAttributes',
        AuditableModel::EventUserUnassigned => 'getDetachedEventAttributes',
        AuditableModel::EventManagerUnassigned => 'getDetachedEventAttributes',
        AuditableModel::EventProviderUnassigned => 'getDetachedEventAttributes',
        AuditableModel::EventProviderNotified => 'getProviderNotifiedEventAttributes'
    ];

    protected function getUniqueIDTemplate()
    {
        $this->load(['relation' => function ($q) {
            $q->with('unit.building.quarter:id,internal_quarter_id', 'quarter:id,internal_quarter_id');
        }]);
        $relation = $this->relation;

        if (empty($relation)) {
            $this->load(['resident' => function ($q) {
                $q->with([
                    'relations' => function ($q) {
                        $q->with('unit.building.quarter:id,internal_quarter_id', 'quarter:id,internal_quarter_id')->first();
                    }
                ]);
            }]);
            $relation = $this->resident->relations->first() ?? null;
        }

        if ($relation) {
            $internalId = $relation->unit->building->internal_building_id
                ?? $relation->unit->building->quarter->internal_quarter_id
                ?? $relation->quarter->internal_quarter_id
                ?? '';

            $unit = $relation->unit->name ?? '';
            $text = '';

            if ($internalId) {
                $text .= $internalId .  '_';
            }

            if ($unit) {
                $text .= $unit;
            }

            return $text ? $text . '-ID' : 'ID';
        }

        return 'RE-ID';
    }


    /**
     * @return array
     */
    public function getProviderNotifiedEventAttributes(): array
    {
        $sp = $this->auditData['serviceProvider'];
        $propertyManager = $this->auditData['propertyManager'];
        $mailDetails = $this->auditData['mailDetails'];
        unset($this->auditData);
        $newValues = [
            'service_provider' => $sp->id,
            'email_title' => $mailDetails['title'],
            'email_cc' => $mailDetails['cc'],
            'email_bcc' => $mailDetails['bcc'],
            'email_to' => $mailDetails['to'],
            'email_body' => $mailDetails['body'],
        ];

        if ($propertyManager) {
            $newValues['property_manager_id'] = $propertyManager->id;
        }

        return [
            [],
            $newValues
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'request_tag', 'request_id');
    }

    public function managers()
    {
        return $this->morphedByMany(PropertyManager::class, 'assignee', 'request_assignees', 'request_id');
    }

    public function property_managers()
    {
        return $this->morphedByMany(PropertyManager::class, 'assignee', 'request_assignees', 'request_id');
    }

    public function providers()
    {
        return $this->morphedByMany(ServiceProvider::class, 'assignee', 'request_assignees', 'request_id');
    }

    public function service_providers()
    {
        return $this->morphedByMany(ServiceProvider::class, 'assignee', 'request_assignees', 'request_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'request_assignees');
    }

    public function assignees()
    {
        return $this->hasMany(RequestAssignee::class, 'request_id');
    }

    public function conversations()
    {
        return $this->morphMany(Conversation::class, 'conversationable');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_user_id', 'id');
    }

    public function conversationFor($u1, $u2)
    {
        if ($u1->id == $u2->id) {
            return null;
        }

        foreach ($this->conversations as $c) {
            if (count($c->users) == 2 &&
                $c->users->contains($u1->id) &&
                $c->users->contains($u2->id))
            {
                return $c;
            }
        }

        $conv = new Conversation();
        $this->conversations()->save($conv);
        $conv->users()->attach($u1);
        $conv->users()->attach($u2);

        return $conv;
    }

    public function requestsNew()
    {
        return $this->where('status', Request::StatusNew);
    }

    public function requestsInProcessing()
    {
        return $this->where('status', Request::StatusInProcessing);
    }

    public function requestsPending()
    {
        return $this->where('status', Request::StatusPending);
    }

    public function requestsDone()
    {
        return $this->where('status', Request::StatusDone);
    }

    public function requestsWarrantyClaim()
    {
        return $this->where('status', Request::StatusWarrantyClaim);
    }

    public function requestsArchived()
    {
        return $this->where('status', Request::StatusArchived);
    }

//    public function getPriorityStrAttribute()
//    {
//        return self::Priority[$this->priority];
//    }

//    public function getInternalPriorityStrAttribute()
//    {
//        return self::Priority[$this->internal_priority];
//    }

    public function getAllPeopleAttribute()
    {
        // @TODO need property managers
        $providers = $this->providers->map(function($p) {
            return $p->user;
        });
        return array_merge([
            $this->resident->user,
        ], $providers->all(), $this->users->all()) ;
    }

    public function setDownloadPdf($settings = null)
    {
        $data = [
        	'datas'=>[
				$this->getDownloadAllPdfData()
			]
        ];
	
        $data['logo'] = $settings->logo ?? null;
        $data['blank_pdf'] = $settings->blank_pdf;
        $data['pdf_font_family'] = $settings->pdf_font_family;
        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('pdfs.request.requestDownloadPdf', $data);
        
        return Storage::disk('request_downloads')->put($this->pdfFileName(), $pdf->output());
    }
    
    public function getDownloadAllPdfData ()
	{
		$media = $this->media->reject(function($m){
			return  ($m->mime_type != config('filesystems.mime_types.jpeg') && $m->mime_type != config('filesystems.mime_types.png'));
		})->map(function($m){
			return ['title' => $m->name, 'file_path' => $m->getPath()];
		});
		
		$data = [
			'category' => get_category_details($this->category_id),
			'subCategory' => get_sub_category_details($this->sub_category_id),
			'request' => $this,
			'resident' => $this->resident,
			'relation' => $this->relation,
			'media' => $media,
		];
  
		return $data;
	}
	
	public function setDownloadAllPdf ($data, $settings = null)
	{
		$data['logo'] = $settings->logo ?? null;;
		$data['blank_pdf']=false;
		$data['pdf_font_family']= $settings->pdf_font_family ?? '';
		$pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('pdfs.request.requestDownloadPdf', $data);
		
		return Storage::disk('request_downloads')->put($this->pdfFileName(), $pdf->output());
	}

    public function getDiskPreName()
    {
        return 'requests_';
    }

    public function pdfFileName()
    {
        $language  = \App::getLocale();

        return $this->id . '-'. $this->resident->id .'-' . $language . '.pdf';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function relation()
    {
        return $this->belongsTo(Relation::class);
    }

    /**
     * @return array
     */
    public function getRequestStatusColumnsAttribute()
    {
        $statusCounts = [];
        foreach (Request::Status as $value) {
            $statusCounts[] = 'requests_' . $value . '_count';
        }

        $statusCounts[] = 'requests_count';
        return $statusCounts;
    }
}
