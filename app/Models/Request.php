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
 *          property="contract_id",
 *          description="contract_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *     @SWG\Property(
 *          property="reminder_user_id",
 *          description="reminder_user_id",
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
 *          property="qualification",
 *          description="qualification",
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
 * @property int $unit_id
 * @property int $resident_id
 * @property int|null $contract_id
 * @property string $request_format
 * @property string $title
 * @property string $description
 * @property int $status
 * @property int $priority
 * @property int $internal_priority
 * @property int $qualification
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
 * @property int|null $reminder_user_id
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
 * @property-read \App\Models\RequestCategory $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\Contract|null $contract
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
 * @property-read \App\Models\User|null $remainder_user
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereContractId($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request whereQualification($value)
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

    const StatusReceived = 1;
    const StatusInProcessing = 2;
    const StatusAssigned = 3;
    const StatusDone = 4;
    const StatusReactivated = 5;
    const StatusArchived = 6;

    const VisibilityResident = 1;
    const VisibilityBuilding = 2;
    const VisibilityQuarter = 3;

    const PendingStatuses = [
        self::StatusReceived,
        self::StatusInProcessing,
        self::StatusAssigned,
        self::StatusReactivated,
    ];

    const SolvedStatuses = [
        self::StatusDone,
        self::StatusArchived,
    ];

    const Status = [
        self::StatusReceived => 'received',
        self::StatusInProcessing => 'in_processing',
        self::StatusAssigned => 'assigned',
        self::StatusDone => 'done',
        self::StatusReactivated => 'reactivated',
        self::StatusArchived => 'archived',
    ];

    const StatusByResident = [
        self::StatusReceived => [self::StatusDone],
        self::StatusAssigned => [self::StatusDone, self::StatusArchived],
        self::StatusInProcessing => [self::StatusDone, self::StatusArchived],
        self::StatusDone => [self::StatusReactivated],
        self::StatusReactivated => [self::StatusDone],
        self::StatusArchived => [],
    ];

    const StatusByService = [
        self::StatusReceived => [],
        self::StatusInProcessing => [self::StatusDone],
        self::StatusAssigned => [self::StatusDone],
        self::StatusDone => [self::StatusReactivated],
        self::StatusReactivated => [self::StatusDone],
        self::StatusArchived => [],
    ];

    const StatusByAgent = [
        self::StatusReceived => [self::StatusAssigned],
        self::StatusAssigned => [self::StatusInProcessing, self::StatusDone, self::StatusArchived],
        self::StatusInProcessing => [self::StatusDone, self::StatusArchived],
        self::StatusDone => [self::StatusReactivated, self::StatusArchived],
        self::StatusReactivated => [self::StatusDone, self::StatusArchived],
        self::StatusArchived => [self::StatusReactivated],
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

    const Qualification = [
        1 => 'none',
        2 => 'optical',
        3 => 'sia',
        4 => '2_year_warranty',
        5 => 'cost_consequences',
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

    const PayerLandlord = 1;
    const PayerResident = 2;
    const PayerResidentLandlord = 3;
    const Payer = [
        self::PayerLandlord => 'landlord',
        self::PayerResident => 'resident',
        self::PayerResidentLandlord => 'resident/landlord',
    ];

    const CategoryGeneral = 1;
    const CategoryMalfunction = 2;
    const CategoryDeficiency = 3;

    const Category = [
        self::CategoryGeneral => 'general',
        self::CategoryMalfunction => 'malfunction',
        self::CategoryDeficiency => 'deficiency',
    ];

    const Fillable = [
        'creator_user_id',
        'reminder_user_id',
        'category_id',
        'subject_id',
        'resident_id',
        'contract_id',
        'unit_id',
        'title',
        'description',
        'status',
//        'priority',
//        'internal_priority',
        'due_date',
        'solved_date',
        'qualification',
        'visibility',
        'request_format',
        'room',
        'capture_phase',
        'component',
        'payer',
        'location',
        'reactivation_date',
        'resolution_time',
        'days_left_due_date',
        'active_reminder',
        'is_public',
        'notify_email',
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
        'creator_user_id' => 'integer',
        'reminder_user_id' => 'integer',
        'resident_id' => 'integer',
        'contract_id' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'status' => 'integer',
//        'priority' => 'integer',
//        'internal_priority' => 'integer',
        'due_date' => 'date',
        'solved_date' => 'datetime',
        'qualification' => 'integer',
        'visibility' => 'integer',
        'sent_reminder_user_ids' => 'array',
        'request_format' => 'string',
        'room' => 'integer',
        'capture_phase' => 'integer',
        'component' => 'string',
        'payer' => 'integer',
        'location' => 'integer',
        'reactivation_date' => 'datetime',
        'resolution_time' => 'integer',
        'days_left_due_date' => 'integer',
        'active_reminder' => 'boolean',
        'is_public' => 'boolean',
        'notify_email' => 'boolean',
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
    public static $rulesPost = [
        'resident_id' => 'required|exists:residents,id',
        'contract_id' => 'required|exists:contracts,id',
        'title' => 'required|string',
        'description' => 'required|string',
//        'priority' => 'required|integer',
//        'internal_priority' => 'integer',
        'qualification' => 'required|integer',
        'due_date' => 'required|date',
        'category_id' => 'required|integer',
        'visibility' => 'required|integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rulesPostResident = [
        'resident_id' => 'exists:residents,id',
        'contract_id' => 'required|exists:contracts,id',
        'title' => 'required|string',
        'description' => 'required|string',
        'category_id' => 'required|integer',
//        'priority' => 'required|integer',
//        'internal_priority' => 'integer',
        'visibility' => 'required|integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rulesPut = [
        'resident_id' => 'exists:residents,id',
        'contract_id' => 'exists:contracts,id',
        'title' => 'string',
        'description' => 'string',
//        'priority' => 'integer',
//        'internal_priority' => 'integer',
        'qualification' => 'integer',
        'status' => 'integer',
        'due_date' => 'date',
        'category_id' => 'integer',
        'visibility' => 'required|integer',
        'active_reminder' => 'boolean',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rulesPutResident = [
        'resident_id' => 'exists:residents,id',
        'contract_id' => 'exists:contracts,id',
        'title' => 'string',
        'description' => 'string',
        'status' => 'integer',
//        'priority' => 'required|integer',
//        'internal_priority' => 'integer',
        'visibility' => 'required|integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rulesPutService = [
        'status' => 'integer'
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
        $this->load(['contract' => function ($q) {
            $q->with('unit', 'building.quarter:id,internal_quarter_id');
        }]);
        $contract = $this->contract;
        if (empty($contract)) {
            $this->load(['resident' => function ($q) {
                $q->with([
                    'contracts' => function ($q) {
                        $q->with('unit', 'building.quarter:id,internal_quarter_id')->first();
                    }
                ]);
            }]);
            $contract = $this->resident->contracts->first() ?? null;
        }
        if ($contract) {
            $internalId = $contract->building->internal_building_id
                ?? $contract->building->quarter->internal_quarter_id
                ?? '';

            $unit = $contract->unit->name ?? '';
            return $internalId .'_' . $unit. '_ID';
        }
        return 'RE_ID';
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
            'service_provider' => [
                'id' => $sp->id,
                'name' => $sp->name
            ],
            'email_title' => $mailDetails['title'],
            'email_cc' => $mailDetails['cc'],
            'email_bcc' => $mailDetails['bcc'],
            'email_to' => $mailDetails['to'],
            'email_body' => $mailDetails['body'],
        ];

        if ($propertyManager) {
            $newValues['property_manager'] = [
                'id' => $propertyManager->id,
                'first_name' => $propertyManager->first_name,
                'last_name' => $propertyManager->last_name,
            ];
        }


        return [
            [],
            $newValues
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function category()
    {
        return $this->hasOne(RequestCategory::class, 'id', 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function unit()
    {
        return $this->belongsTo(Unit::class);
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

    public function providers()
    {
        return $this->morphedByMany(ServiceProvider::class, 'assignee', 'request_assignees', 'request_id');
    }

    public function users()
    {
        return $this->morphedByMany(User::class, 'assignee', 'request_assignees', 'request_id');
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
                $c->users->contains($u2->id)) {
                return $c;
            }
        }

        $conv = new Conversation();
        $this->conversations()->save($conv);
        $conv->users()->attach($u1);
        $conv->users()->attach($u2);

        return $conv;
    }

    public function requestsReceived()
    {
        return $this->where('status', Request::StatusReceived);
    }

    public function requestsInProcessing()
    {
        return $this->where('status', Request::StatusInProcessing);
    }

    public function requestsAssigned()
    {
        return $this->where('status', Request::StatusAssigned);
    }

    public function requestsDone()
    {
        return $this->where('status', Request::StatusDone);
    }

    public function requestsReactivated()
    {
        return $this->where('status', Request::StatusReactivated);
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

    public function setDownloadPdf(){

       $data = [
            'category' => $this->category,
            'request' => $this,
            'resident' => $this->resident,
            'language'  => $this->resident->settings->language
        ];

        $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('pdfs.request.requestDownload', $data);

        return Storage::disk('request_downloads')->put($this->pdfFileName(), $pdf->output());
    }

    public function getDiskPreName()
    {
        return 'requests_';
    }

    public function pdfFileName()
    {
        $language  = $this->resident->settings->language;

        return $this->id . '-'. $this->resident->id .'-' . $language . '.pdf';
    }

    public function remainder_user()
    {
        return $this->belongsTo(User::class, 'reminder_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
