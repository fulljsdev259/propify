<?php

namespace App\Repositories;

use App\Models\AuditableModel;
use App\Mails\NotifyServiceProvider;
use App\Models\Comment;
use App\Models\Model;
use App\Models\PropertyManager;
use App\Models\Contract;
use App\Models\ServiceProvider;
use App\Models\Request;
use App\Models\RequestCategory;
use App\Models\Resident;
use App\Models\User;
use App\Notifications\NewResidentRequest;
use App\Notifications\RequestCommented;
use App\Notifications\RequestDue;
use App\Notifications\RequestMedia;
use App\Notifications\StatusChangedRequest;
use App\Traits\SaveMediaUploads;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\Models\Media;

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
        'solved_date' => '>=',
        'created_at' => '>=',
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
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes)
    {
        $attributes = self::getPostAttributes($attributes);
        if (isset($attributes['category'])) {
            $categoryAttributes = Request::CategoryAttributes[$attributes['category']] ?? [];
            if (empty($categoryAttributes) || ! in_array(Request::HasQualifications, $categoryAttributes)) {
                unset($attributes['qualification']);
            }
        }

        $attributes = $this->fixContractRelated($attributes);
        $attributes['creator_user_id'] = Auth::id();
        $model = parent::create($attributes);
        if ($model)  {
            $model = $this->saveMediaUploads($model, $attributes);
        }
        return $model;
    }

    /**
     * @param $attributes
     * @return array
     */
    private static function getPostAttributes($attributes)
    {
        $user = Auth::user();
        if ($user->resident) {
            $attr = [];
            $attr['title'] = $attributes['title'];
            $attr['description'] = $attributes['description'];
            $attr['contract_id'] = $attributes['contract_id'];
            $attr['category'] = $attributes['category'];
            $attr['sub_category'] = $attributes['sub_category'];
            $attr['visibility'] = $attributes['visibility'];
           // $attr['priority'] = $attributes['priority'];
          //  $attr['internal_priority'] = $attributes['internal_priority'] ?? $attributes['priority'];
            $attr['resident_id'] = $user->resident->id;
            $attr['status'] = Request::StatusReceived;
            $attr['qualification'] = array_flip(Request::Qualification)['none'];
            return $attr;
        }

        // already checked resident exists
        $attributes['assignee_ids'] = [Auth::user()->id]; // @TODO where used
        $attributes['status'] = Request::StatusReceived;
        $attributes['due_date'] = Carbon::parse($attributes['due_date'])->format('Y-m-d');

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
            $attr['category'] = $attributes['category'];
            $attr['sub_category'] = $attributes['sub_category'];
            $attr['status'] = $attributes['status'];

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
        $attributes = $this->getPutAttributes($attributes, $model);
        $attributes = $this->getStatusRelatedAttributes($attributes, $model);
        $attributes = $this->fixContractRelated($attributes);
        $oldModel = clone $model;
        $updatedModel =  parent::updateExisting($model, $attributes);

        if ($updatedModel) {
            $this->notifyStatusChangeIfNeed($oldModel, $updatedModel);

            if ($updatedModel->due_date && $updatedModel->due_date != $oldModel->due_date) {
                $this->notifyDue($updatedModel);
            }
        }

        return $updatedModel;
    }

    /**
     * @param $attributes
     * @return mixed
     */
    protected function fixContractRelated($attributes)
    {
        if (!empty($attributes['contract_id'])) {
            // already validated and it must be exists
            $contract = Contract::find($attributes['contract_id'], ['id', 'resident_id', 'unit_id']);
            $attributes['resident_id'] = $contract->resident_id;
            $attributes['unit_id'] = $contract->unit_id;
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
            } elseif (Request::StatusReactivated == $attributes['status']) {
                $attributes['reactivation_date'] = now();
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

    /**
     * @param Request $originalRequest
     * @param Request $request
     */
    public function notifyStatusChangeIfNeed(Request $originalRequest, Request $request)
    {
        if ($originalRequest->status != $request->status) {
            $user = $request->resident->user;
            $user->notify(new StatusChangedRequest($request, $originalRequest, $user));
        }
    }

    /**
     * @param Request $request
     */
    public function notifyNewRequest(Request $request)
    {
        $contract = $request->contract;
        if (! $contract->building) {
            return;
        }

        $propertyManagers = PropertyManager::whereHas('buildings', function ($q) use ($contract) {
            $q->where('buildings.id', $contract->building->id);
        })->get();

        $i = 0;
        foreach ($propertyManagers as $propertyManager) {
            $delay = $i++ * env("DELAY_BETWEEN_EMAILS", 10);
            $propertyManager->user->redirect = "/admin/requests/" . $request->id;

            $propertyManager->user
                ->notify((new NewResidentRequest($request, $propertyManager->user, $contract->resident->user))
                    ->delay(now()->addSeconds($delay)));
        }
    }

    /**
     * @param Request $request
     * @param Comment $comment
     */
    public function notifyNewComment(Request $request, Comment $comment)
    {
        $i = 0;
        foreach ($request->allPeople as $person) {
            $delay = $i++ * env("DELAY_BETWEEN_EMAILS", 10);

            if ($person->id != $comment->user->id) {
                $person->notify((new RequestCommented($request, $person, $comment))
                    ->delay(now()->addSeconds($delay)));
            }
        }
    }

    /**
     * @param Request $request
     * @param User $uploader
     * @param Media $media
     */
    public function notifyMedia(Request $request, User $uploader, Media $media)
    {
        $i = 0;
        foreach ($request->allPeople as $person) {
            $delay = $i++ * env("DELAY_BETWEEN_EMAILS", 10);

            if ($person->id != $uploader->id) {
                $person->notify((new RequestMedia($request, $uploader, $media))
                    ->delay(now()->addSeconds($delay)));
            }
        }
    }

    /**
     * @param Request $request
     * @param ServiceProvider $provider
     * @param $manager
     * @param $mailDetails
     */
    public function notifyProvider(Request $request, ServiceProvider $provider, $manager, $mailDetails)
    {
        $toEmails = [$provider->user->email];
        if (!empty($mailDetails['to'])) {
            $toEmails[] = $mailDetails['to'];
        }

        $ccEmails = $manager ? [$manager->user->email] : [];
        if (!empty($mailDetails['cc']) && is_array($mailDetails['cc'])) {
            $ccEmails = array_merge($ccEmails, $mailDetails['cc']);
        }

        $bccEmails = $mailDetails['bcc'] ?? [];
        \Mail::to($toEmails)
            ->cc($ccEmails)
            ->bcc($bccEmails)
            ->send( new NotifyServiceProvider($provider, $request, $mailDetails));

        $auditData = [
            'serviceProvider' => $provider,
            'propertyManager' => $manager,
            'mailDetails' => $mailDetails
        ];
        $request->registerAuditEvent(AuditableModel::EventProviderNotified, $auditData);

        $u = \Auth::user();
        $conversation = $request->conversationFor($u, $provider->user);
        $comment = $mailDetails['title'] . "\n\n" . strip_tags($mailDetails['body']);
        $conversation->comment($comment);

        if ($manager && $manager->user) {
            $conversation = $request->conversationFor($u, $manager->user);
            if ($conversation) {
                $conversation->comment($comment);
            }
        }
    }

    /**
     * @param Request $request
     */
    public function notifyDue(Request $request)
    {
        $beforeHours = env('REQUEST_DUE_MAIL', 24);
        $providerUsers = $request->providers()->with('user')->get()->pluck('user')->all();
        $managerUsers = $request->managers()->with('user')->get()->pluck('user')->all();

        foreach (array_merge($providerUsers, $managerUsers) as $u) {
            $u->notify((new RequestDue($request))->delay($request->due_date->subHours($beforeHours)));
        }
    }

    public function deleteRequesetWithUnitIds($ids)
    {
        return $this->model->whereIn('unit_id', $ids)->delete();
    }

    public function getRequestCountWithUnitIds($ids)
    {
        return $this->model->whereIn('unit_id', $ids)->count();
    }

    /**
     * @param $requests
     * @param $relation
     * @param $items
     */
    public function massAssign($requests, $relation, $items)
    {
        foreach ($requests as $request) {
            foreach ($items as $item) {
                $request->{$relation}()->sync([$item->id => ['created_at' => now()]], false);
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
