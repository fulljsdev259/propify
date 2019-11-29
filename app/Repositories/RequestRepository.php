<?php

namespace App\Repositories;

use App\Jobs\Notify\NotifyNewRequest;
use App\Jobs\Notify\NotifyEmailReceptionistsNewPublicRequest;
use App\Jobs\Notify\NotifyRequestDue;
use App\Jobs\Notify\NotifyRequestStatusChange;
use App\Models\Model;
use App\Models\PropertyManager;
use App\Models\Relation;
use App\Models\ServiceProvider;
use App\Models\Request;
use App\Models\Workflow;
use App\Traits\SaveMediaUploads;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class RequestRepository
 * @package App\Repositories
 * @version February 27, 2019, 2:18 pm UTC
 *
 * @method Request findWithoutFail($id, $columns = ['*'])
 * @method Request find($id, $columns = ['*'])
 * @method Request first($columns = ['*'])
 */
class RequestRepository extends BaseRepository
{
    use  SaveMediaUploads;
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id' => 'like',
        'title' => 'like',
        'description' => 'like',
        'status' => 'like',
       // 'priority' => 'like',
       // 'internal_priority' => 'like',
        'due_date' => '=',
//        'solved_date' => '=',
//        'created_at' => '>=',
        'request_format' => 'like',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Request::class;
    }

    /**
     * @param array $attributes
     * @return Model|Request|mixed
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes)
    {
        $attributes = self::getPostAttributes($attributes);
        $attributes = $this->fixRelationRelated($attributes);
        $attributes['creator_user_id'] = Auth::id();

        if (key_exists('send_notification', $attributes)) {
            $attributes['notify_email'] = $attributes['send_notification'];
        }

        /**
         * @var  $request Request
         */
        $request = parent::create($attributes);
        if (empty($request))  {
            return $request;
        }

        $request = $this->assignAdminsToRequest($request);
        $request = $this->saveMediaUploads($request, $attributes);
        $notificationsData = dispatch_now(new NotifyNewRequest($request, false));

        if ($request->notify_email) {
            $receptionistsNotifications = dispatch_now(new NotifyEmailReceptionistsNewPublicRequest($request, false));
            $notificationsData = $notificationsData->merge($receptionistsNotifications);
        }

        if ($request->due_date) {
            $dueNotifications  = dispatch_now(new NotifyRequestDue($request, false));
            $notificationsData = $notificationsData->merge($dueNotifications);
        }

        // save all sent notification data
        $request->newSystemNotificationAudit($notificationsData);
        return $request;
    }

    /**
     * @param Request $request
     * @return Request
     */
    protected function assignAdminsToRequest(Request $request)
    {
        $building = $request->relation->building ?? null;
        if (empty($building)) {
            return $request;
        }
        $workflows = Workflow::where('category_id', $request->category_id)
            ->where('quarter_id', $building->quarter_id)
            ->where('building_ids', 'like', '%' . $building->id .'%')
            ->get();
        $workflows = $workflows->filter(function ($workflow) use ($building) {
            return in_array($building->id, $workflow->building_ids);
        });

        $userIds = $workflows->pluck('to_user_ids')
            ->merge($workflows->pluck('cc_user_ids'))
            ->collapse()
            ->unique();

        if ($userIds->isEmpty()) {
            return $request;
        }

        Request::disableAuditing();
        // @TODO save system audit for assignment
        $propertyManagers = PropertyManager::whereIn('user_id', $userIds)->get(['id']);
        $data = [];
        foreach ($propertyManagers as $propertyManager) {
            $data[$propertyManager->id] = ['created_at' => now()];
        }

        if ($data) {
            $request->property_managers()->syncWithoutDetaching($data);
        }

        $serviceProviders = ServiceProvider::whereIn('user_id', $userIds)->get(['id']);

        $data = [];
        foreach ($serviceProviders as $serviceProvider) {
            $data[$serviceProvider->id] = ['created_at' => now()];
        }

        if ($data) {
            $request->service_providers()->syncWithoutDetaching($data);
        }
        Request::enableAuditing();
        return $request;
    }

    /**
     * @param $attributes
     * @return array
     */
    private static function getPostAttributes($attributes)
    {
        $user = Auth::user();
        if ($user->resident) {
            // @TODO later unset not permitted fields
            $attr = [];
            $attr['title'] = $attributes['title'];
            $attr['description'] = $attributes['description'];
            $attr['relation_id'] = $attributes['relation_id'];
            $attr['category_id'] = $attributes['category_id'];

            $attr['sub_category_id'] = $attributes['sub_category_id'];
            $attr['visibility'] = $attributes['visibility'];
            $attr['resident_id'] = $user->resident->id;
            $attr['status'] = Request::StatusNew;

            // $attr['priority'] = $attributes['priority'];
            //  $attr['internal_priority'] = $attributes['internal_priority'] ?? $attributes['priority'];
            $attr = self::fixNeededData($attr, $attributes, 'location');
            $attr = self::fixNeededData($attr, $attributes, 'is_public');
            $attr = self::fixNeededData($attr, $attributes, 'room');
            $attr = self::fixNeededData($attr, $attributes, 'percentage');
            $attr = self::fixNeededData($attr, $attributes, 'qualification_category');
            $attr = self::fixNeededData($attr, $attributes, 'amount');

            // @TODO maybe need
//            $attr = self::fixNeededData($attr, $attributes, 'capture_phase');
//            $attr = self::fixNeededData($attr, $attributes, 'component');


            return $attr;
        }

        // already checked resident exists
        $attributes['assignee_ids'] = [Auth::user()->id]; // @TODO where used
        $attributes['status'] = Request::StatusNew;
        $attributes['due_date'] = Carbon::parse($attributes['due_date'])->format('Y-m-d');

        return $attributes;
    }

    /**
     * @param $attributes
     * @param $data
     * @param $key
     * @return mixed
     */
    private static function fixNeededData($attributes, $data, $key)
    {
        if (key_exists($key, $data)) {
            $attributes[$key] = $data[$key];
        }
        return $attributes;
    }


    /**
     * @param $attributes
     * @param $request
     * @return array
     */
    public static function getPutAttributes($attributes, $request)
    {
        $user = Auth::user();
        if ($user->resident) {
            // I think it is resident permitted options
            $attr = [];
            $attr['title'] = $attributes['title'];
            $attr['description'] = $attributes['description'];
            $attr['category_id'] = $attributes['category_id'];
            $attr['sub_category_id'] = $attributes['sub_category_id'];
            $attr['status'] = $attributes['status'];
            $attr = self::fixNeededData($attr, $attributes, 'qualification_category');
            return $attr;
        }

        $attributes = self::getStatusRelatedAttributes($attributes, $request);

        if (isset($attributes['due_date'])) {
            $attributes['due_date'] = Carbon::parse($attributes['due_date'])->format('Y-m-d');
        }

        return $attributes;
    }

    /**
     * @param Model $model
     * @param $attributes
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateExisting(Model $model, $attributes)
    {
        \DB::beginTransaction();
        $attributes = $this->getPutAttributes($attributes, $model);
        $attributes = $this->getStatusRelatedAttributes($attributes, $model);
        $attributes = $this->fixRelationRelated($attributes);
        $oldModel = clone $model;
        $updatedModel =  parent::updateExisting($model, $attributes);
        $updatedModel = $this->saveMediaUploads($updatedModel, $attributes);

        if (!$updatedModel) {
            \DB::rollBack();
            return $updatedModel;
        }

        $notificationsData = dispatch_now(new NotifyRequestStatusChange($oldModel, $updatedModel, false));
        if ($updatedModel->due_date && $updatedModel->due_date != $oldModel->due_date) {
            $notifyRequestDue = dispatch_now(new NotifyRequestDue($updatedModel, false));
            $notificationsData = $notificationsData->merge($notifyRequestDue);
        }

        // save all sent notification data
        $updatedModel->newSystemNotificationAudit($notificationsData);
        \DB::commit();

        return $updatedModel;
    }

    /**
     * @param $attributes
     * @return mixed
     */
    protected function fixRelationRelated($attributes)
    {
        if (!empty($attributes['relation_id'])) {
            // already validated and it must be exists
            $relation = Relation::find($attributes['relation_id'], ['id', 'resident_id']);
            $attributes['resident_id'] = $relation->resident_id;
        }

        return $attributes;
    }

    public static function getStatusRelatedAttributes($attributes, $request)
    {
        if (isset($attributes['status']) && $attributes['status'] != $request->status) {
            if (Request::StatusDone == $attributes['status']) {
                $now = now();
                $attributes['solved_date'] = $now;
                $time = $request->reactivation_date ?? $request->created_at;
                $attributes['resolution_time'] = $request->resolution_time + $now->diffInSeconds($time);
//            } elseif (Request::StatusReactivated == $attributes['status']) {
//                $attributes['reactivation_date'] = now();
            }
        }
        return $attributes;
    }

    /**
     * @param $attributes
     * @param $currentStatus
     * @return bool
     */
    public function checkStatusPermission($attributes, $currentStatus)
    {
        if (!isset($attributes['status']) || $currentStatus == $attributes['status']) {
            return true;
        }
        return true;
        $user = Auth::user();
        if ($user->resident) {
            if (!in_array($attributes['status'], Request::StatusByResident[$currentStatus])) {
                return false;
            }
        }

        if (!in_array($attributes['status'], Request::StatusByAgent[$currentStatus])) {
            return false;
        }

        return true;
    }

    public function deleteRequesetWithUnitIds($ids)
    {
        return $this->model->whereHas('relation', function ($q) use ($ids) {
            $q->whereIn('unit_id', $ids);
        })->delete();
    }

    public function getRequestCountWithUnitIds($ids)
    {
        return $this->model->whereHas('relation', function ($q) use ($ids) {
            $q->whereIn('unit_id', $ids);
        })->count();
    }

    /**
     * @param $requests
     * @param $relation
     * @param $items
     * @param $sentEmail
     */
    public function massAssign($requests, $relation, $items, $sentEmail)
    {
        foreach ($requests as $request) {
            foreach ($items as $item) {
                $request->{$relation}()->sync([$item->id => ['created_at' => now(),'sent_email' => $sentEmail]], false);
            }
        }
    }

    /**
     * @param $requests
     * @param $data
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function massUpdateAttribute($requests, $data)
    {
        foreach ($requests as $request) {
            $this->updateExisting($request, $data);
        }
    }
}
