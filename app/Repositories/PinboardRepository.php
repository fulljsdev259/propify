<?php

namespace App\Repositories;

use App\Models\Building;
use App\Models\Model;
use App\Models\Quarter;
use App\Models\Pinboard;
use App\Models\RentContract;
use App\Models\Resident;
use App\Models\Settings;
use App\Models\User;
use App\Notifications\NewResidentInNeighbour;
use App\Notifications\NewResidentPinboard;
use App\Notifications\AnnouncementPinboardPublished;
use App\Notifications\PinboardPublished;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Class PinboardRepository
 * @package App\Repositories
 * @version February 11, 2019, 6:22 pm UTC
 *
 * @method Pinboard findWithoutFail($id, $columns = ['*'])
 * @method Pinboard find($id, $columns = ['*'])
 * @method Pinboard first($columns = ['*'])
*/
class PinboardRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'content' => 'like',
    ];

    /**
     * @var array
     */
    protected $mimeToExtension = [
        "image/jpeg" =>  "jpg",
        "image/png" =>  "png",
    ];


    /**
     * Configure the Model
     **/
    public function model()
    {
        return Pinboard::class;
    }

    /**
     * @param array $atts
     * @return Pinboard|mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $atts)
    {
        $atts['building_ids'] = $atts['building_ids'] ?? [];
        $atts['quarter_ids'] = $atts['quarter_ids'] ?? [];
        $u = \Auth::user();
        if ($u->resident()->exists()) {
            $rentContracts = $u->resident->active_rent_contracts_with_building()->get(['building_id']);
            if ($rentContracts->isEmpty()) {
                throw new \Exception("Your resident account does not have any active rent contract");
            }

            $rentContracts->load('building:id,quarter_id');
            $atts['building_ids'] = $rentContracts->pluck('building_id')->unique()->toArray();
            if (!empty($atts['visibility']) && Pinboard::VisibilityQuarter == $atts['visibility']) {
                $quarterIds = $rentContracts->where('building.quarter_id', '!=', null)->pluck('building.quarter_id');
                $atts['quarter_ids'] = $quarterIds->unique()->toArray();
            } else {
                $atts['quarter_ids'] = [];
            }
        }

        if (isset($atts['category_image'])) {
            $atts['category_image'] = ($atts['category_image'] == 'true') ? 1 : 0;
        } else {
            $atts['category_image'] = 0;
        }

        $atts = $this->fixBollInt($atts, 'is_execution_time', 1);

        if (! $atts['needs_approval']) {
            // @TODO correct this things
            $atts['status'] = Pinboard::StatusPublished;
        }

        if (Pinboard::StatusPublished == $atts['status']) {
            $atts['published_at'] = now();
        }

        $model = parent::create($atts);
        if (!empty($atts['quarter_ids'])) {
            $model->quarters()->sync($atts['quarter_ids']);
        }

        if (!empty($atts['building_ids'])) {
            $model->buildings()->sync($atts['building_ids']);
        }

        $notificationsData = collect();
        if (Pinboard::StatusPublished == $atts['status']) {
            $notificationsData = $this->notify($model);
        }
        $adminNotificationsData = $this->notifyAdminNewResidentPinboard($model);
        $notificationsData = $notificationsData->merge($adminNotificationsData);
        $this->saveNotificationAuditsAndLogs($model, $notificationsData);
//        $this->notifyAdminActions($model);
        return $model;
    }

    protected function saveNotificationAuditsAndLogs(Pinboard $pinboard, $notificationsData)
    {
        $announcementPinboardPublished = get_morph_type_of(AnnouncementPinboardPublished::class);
        $announcementPinboardPublishedUsers = $notificationsData[$announcementPinboardPublished] ?? collect();
        if ($announcementPinboardPublishedUsers->isNotEmpty()) {
            $pinboard->announcement_email_receptionists()->create([
                'resident_ids' => $announcementPinboardPublishedUsers->pluck('resident.id'),
                'failed_resident_ids' => []
            ]);
        }

        $pinboard->addDataInAudit('notifications', $notificationsData);
    }


    /**
     * @param int $id
     * @param $status
     * @param $publishedAt
     * @return Pinboard|mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function setStatus(int $id, $status, $publishedAt)
    {
        $pinboard = $this->find($id);
        return $this->setStatusExisting($pinboard, $status, $publishedAt);
    }

    /**
     * @param Pinboard $pinboard
     * @param $status
     * @param $publishedAt
     * @return Pinboard|mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function setStatusExisting(Pinboard $pinboard, $status, $publishedAt)
    {
        if ($pinboard->status == $status) {
            return $pinboard;
        }

        return $this->updateExisting($pinboard, ['status' => $status]);
    }

    /**
     * @param array $attributes
     * @param $id
     * @return Model|mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update(array $attributes, $id)
    {
        $model = $this->model->findOrFail($id);
        return $this->updateExisting($model, $attributes);
    }

    /**
     * @param Model $model
     * @param $attributes
     * @return Model|mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateExisting(Model $model, $attributes)
    {
        $attributes = $this->fixBollInt($attributes, 'is_execution_time', 1);
        $status= $attributes['status'] ?? null;
        if (Pinboard::StatusPublished == $status) {
            $attributes['published_at'] = now();
        }
        $model = parent::updateExisting($model, $attributes);

        if (Pinboard::StatusPublished == $status) {
            $notificationsData = $this->notify($model);
            $this->saveNotificationAuditsAndLogs($model, $notificationsData);
        }

        return $model;
    }

    /**
     * @param Pinboard $pinboard
     * @return \Illuminate\Support\Collection
     */
    public function notify(Pinboard $pinboard)
    {
        if (!$pinboard->notify_email) {
            return collect();
        }

        $usersToNotify = $this->getNotifiedResidentUsers($pinboard);

        $announcementPinboardPublished = get_morph_type_of(AnnouncementPinboardPublished::class);
        $pinboardPublished = get_morph_type_of(PinboardPublished::class);
        $pinboardNewResidentNeighbor = get_morph_type_of(NewResidentInNeighbour::class);
        $notificationsData = collect([
            $announcementPinboardPublished => collect(),
            $pinboardPublished => collect(),
            $pinboardNewResidentNeighbor => collect(),
        ]);

        if ($usersToNotify->isEmpty()) {
            return $notificationsData;
        }

        $usersToNotify->load('settings:user_id,admin_notification,pinboard_notification', 'resident:id,user_id,first_name,last_name');
        $i = 0;
        foreach ($usersToNotify as $u) {
            $delay = $i++ * env("DELAY_BETWEEN_EMAILS", 10);
            $u->redirect = '/news';
            if ($u->settings && $u->settings->admin_notification && $pinboard->announcement) {
                $notificationsData[$announcementPinboardPublished]->push($u);
                $u->notify((new AnnouncementPinboardPublished($pinboard))
                    ->delay(now()->addSeconds($delay)));
                continue;
            }
            if ($u->settings && $u->settings->pinboard_notification && ! $pinboard->announcement) {
                if ($pinboard->type == Pinboard::TypePost) {
                    $notificationsData[$pinboardPublished]->push($u);
                    $u->notify(new PinboardPublished($pinboard));
                }
                if ($pinboard->type == Pinboard::TypeNewNeighbour) {
                    $notificationsData[$pinboardNewResidentNeighbor]->push($u);
                    $u->notify((new NewResidentInNeighbour($pinboard))->delay($pinboard->published_at));
                }
            }
        }

        return $notificationsData;
    }

    /**
     * @param Pinboard $pinboard
     * @return Collection
     */
    protected function getNotifiedResidentUsers(Pinboard $pinboard)
    {
        if ($pinboard->visibility == Pinboard::VisibilityAll) {
            return User::whereHas('resident', function ($q) {
                    $q->whereNull('residents.deleted_at');
                })
                ->where('id', '!=', $pinboard->user_id)
                ->get();
        }

        $quarterIds = $buildingIds = [];
        if ($pinboard->visibility == Pinboard::VisibilityQuarter || $pinboard->announcement) {
            $quarterIds = $pinboard->quarters()->pluck('id')->toArray();
        }

        if ($pinboard->visibility == Pinboard::VisibilityAddress  || $pinboard->announcement) {
            $buildingIds = $pinboard->buildings()->pluck('id')->toArray();
        }
        if (empty($quarterIds) && empty($buildingIds)) {
            return $pinboard->newCollection();
        }

        return User::whereHas('resident', function ($q) use ($quarterIds, $buildingIds) {
            $q->whereNull('residents.deleted_at')
                ->where('residents.status', Resident::StatusActive)
                ->whereHas('rent_contracts', function ($q) use ($quarterIds, $buildingIds) {

                    $q->where('status', RentContract::StatusActive)
                        ->when(
                            ! empty($quarterIds) && !empty($buildingIds),
                            function ($q)  use ($quarterIds, $buildingIds) {
                                $q->whereHas('building', function ($q) use ($quarterIds, $buildingIds) {
                                    $q->where(function ($q) use ($quarterIds, $buildingIds) {
                                        $q->whereIn('id', $buildingIds)->orWhereIn('quarter_id', $quarterIds);
                                    })->whereNull('buildings.deleted_at');
                                });
                            },
                            function ($q) use ($quarterIds, $buildingIds) {
                                $q->when(
                                    !empty($quarterIds),
                                    function ($q) use ($quarterIds) {
                                        $q->whereHas('building', function ($q) use ($quarterIds) {
                                            $q  ->whereIn('quarter_id', $quarterIds)
                                                ->whereNull('buildings.deleted_at');
                                        });
                                    },
                                    function ($q) use ($buildingIds) {
                                        $q->whereIn('building_id', $buildingIds);
                                    }
                                );
                            }
                        );
                });
        })->get();
    }

    /**
     * @param Pinboard $pinboard
     */
    public function notifyAdminActions(Pinboard $pinboard)
    {
        if (! Auth::user()->hasRole('administrator')) {
            return;
        }
        // @TODO
    }

    public function notifyAdminNewResidentPinboard(Pinboard $pinboard)
    {
        $newResidentPinboard = get_morph_type_of(NewResidentPinboard::class);
        if (empty($pinboard->user->resident)) {
            return collect([$newResidentPinboard => collect()]);
        }

        $settings = Settings::firstOrFail();
        $admins = User::whereIn('id', $settings->pinboard_receiver_ids)->get();
        $i = 0;
        foreach ($admins as $admin) {
            $delay = $i++ * env("DELAY_BETWEEN_EMAILS", 10);
            $admin->redirect = '/admin/pinboard';

            $notif = (new NewResidentPinboard($pinboard, $admin))->delay(now()->addSeconds($delay));
            $admin->notify($notif);
        }

        return collect([$newResidentPinboard => $admins]);
    }

    /**
     * @param Resident $resident
     * @return Pinboard|bool|mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function newResidentPinboard(Resident $resident)
    {
        if ($resident->homeless()) {
            return false;
        }

        $pinboard = $this->create([
            'visibility' => Pinboard::VisibilityAddress,
            'status' => Pinboard::StatusNew,
            'type' => Pinboard::TypeNewNeighbour,
            'content' => "New neighbour",
            'user_id' => $resident->user->id,
            'building_ids' => [$resident->building->id],
            'needs_approval' => false,
            'notify_email' => true,
        ]);

        $publishStart = $resident->rent_start ?? Carbon::now();
        if ($publishStart->isBefore(Carbon::now())) {
            $publishStart = Carbon::now();
        }

        $this->setStatusExisting($pinboard, Pinboard::StatusPublished, $publishStart);
        return $pinboard;
    }

    /**
     * @param RentContract $rentContract
     * @return Pinboard|bool|mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function newRentContractPinboard(RentContract $rentContract)
    {
        if (empty($rentContract->building_id)) {
            return false;
        }

        $pinboard = $this->create([
            'visibility' => Pinboard::VisibilityAddress,
            'status' => Pinboard::StatusNew,
            'type' => Pinboard::TypeNewNeighbour,
            'content' => "New neighbour",
            'user_id' => $rentContract->resident->user->id,
            'building_ids' => [$rentContract->building_id],
            'needs_approval' => false,
            'notify_email' => true,
        ]);

        $publishStart = $rentContract->start_date ?? Carbon::now();
        if ($publishStart->isBefore(Carbon::now())) {
            $publishStart = Carbon::now();
        }

        $this->setStatusExisting($pinboard, Pinboard::StatusPublished, $publishStart);
        return $pinboard;
    }

    /**
     * @param Pinboard $p
     * @return mixed
     */
    public function locations(Pinboard $p)
    {
        // Cannot use $p->buildings() and $p->quarters() because of a bug
        // related to different number of columns in union
        $pbs = Building::select(\DB::raw('id, name, "building" as type'))
            ->join('building_pinboard', 'building_pinboard.building_id', '=', 'id')
            ->where('building_pinboard.pinboard_id', $p->id);
        $pds = Quarter::select(\DB::raw('id, name, "quarter" as type'))
            ->join('pinboard_quarter', 'pinboard_quarter.quarter_id', '=', 'id')
            ->where('pinboard_quarter.pinboard_id', $p->id);

        return $pbs->union($pds);
    }
}
