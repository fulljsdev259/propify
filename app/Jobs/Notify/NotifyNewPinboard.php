<?php

namespace App\Jobs\Notify;

use App\Models\Relation;
use App\Models\Pinboard;
use App\Models\Resident;
use App\Notifications\AnnouncementPinboardPublished;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Collection;
use App\Notifications\PinboardPublished;
use App\Notifications\NewResidentInNeighbour;
use App\Models\User;

/**
 * Class PinboardNotify
 * @package App\Jobs
 */
class NotifyNewPinboard
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Pinboard
     */
    private $pinboard;

    /**
     * @var bool
     */
    private $saveSystemAudit;

    /**
     * PinboardNotify constructor.
     * @param Pinboard $pinboard
     * @param bool $saveSystemAudit
     */
    public function __construct(Pinboard $pinboard, $saveSystemAudit = true)
    {
        $this->pinboard = $pinboard;
        $this->saveSystemAudit = $saveSystemAudit;
    }

    /**
     * @return \Illuminate\Support\Collection
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function handle()
    {
        $pinboard = $this->pinboard;

        if (empty($pinboard->notify_email)) {
            return collect();
        }

        if ($pinboard->announcement) {
            return $this->newAnnouncementPinboard($pinboard);
        }

        $usersToNotify = $this->getNotifiedResidentUsers($pinboard);

        $pinboardPublished = get_morph_type_of(PinboardPublished::class);
        $pinboardNewResidentNeighbor = get_morph_type_of(NewResidentInNeighbour::class);
        $notificationsData = collect([
            $pinboardPublished => collect(),
            $pinboardNewResidentNeighbor => collect(),
        ]);

        if ($usersToNotify->isEmpty()) {
            return $notificationsData;
        }

        $usersToNotify->load('settings:user_id,admin_notification,pinboard_notification', 'resident:id,user_id,first_name,last_name');
        $i = 0;
        foreach ($usersToNotify as $user) {
            $user->redirect = '/news';

            if (empty($user->settings) || ! $user->settings->pinboard_notification) {
                continue;
            }

            if ($pinboard->type == Pinboard::TypePost) {
                $notificationsData[$pinboardPublished]->push($user);
                $user->notify(new PinboardPublished($pinboard));
            }
            
            if ($pinboard->type == Pinboard::TypeNewNeighbour) {
                $notificationsData[$pinboardNewResidentNeighbor]->push($user);
                $user->notify((new NewResidentInNeighbour($pinboard))->delay($pinboard->published_at));
            }
        }

        if ($this->saveSystemAudit) {
            $pinboard->newSystemNotificationAudit($notificationsData);
        }

        return $notificationsData;
    }

    /**
     * @param Pinboard $pinboard
     * @return \Illuminate\Support\Collection
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    protected function newAnnouncementPinboard(Pinboard $pinboard)
    {
        $announcementPinboardPublished = get_morph_type_of(AnnouncementPinboardPublished::class);
        $notificationsData = collect([
            $announcementPinboardPublished => collect(),
        ]);

        $quarterIds = $buildingIds = [];
        if ($pinboard->visibility == Pinboard::VisibilityQuarter || $pinboard->announcement) {
            $quarterIds = $pinboard->quarters()->pluck('id')->toArray();
        }

        if ($pinboard->visibility == Pinboard::VisibilityAddress  || $pinboard->announcement) {
            $buildingIds = $pinboard->buildings()->pluck('id')->toArray();
        }

        $usersToNotify = $this->getNotifiedResidentUsers($pinboard, $quarterIds, $buildingIds);
        if ($usersToNotify->isEmpty()) {
            return $notificationsData;
        }

        $usersToNotify->load('settings:user_id,admin_notification,pinboard_notification', 'resident:id,user_id,first_name,last_name');
        $i = 0;
        foreach ($usersToNotify as $user) {
            $delay = $i++ * env("DELAY_BETWEEN_EMAILS", 10);
            $user->redirect = '/news';

            if ($user->settings && $user->settings->admin_notification) { // @TODO correct U think it must be pinboard_notification ??
                $notificationsData[$announcementPinboardPublished]->push($user);
                $user->notify((new AnnouncementPinboardPublished($pinboard))
                    ->delay(now()->addSeconds($delay)));
            }
        }

        $announcementPinboardPublishedUsers = $notificationsData[$announcementPinboardPublished] ?? collect();
        $this->saveAnnouncementEmailReceptionists($pinboard, $buildingIds, $quarterIds, $announcementPinboardPublishedUsers);


        if ($this->saveSystemAudit) {
            $pinboard->newSystemNotificationAudit($notificationsData);
        }

        return $notificationsData;
    }

    /**
     * @param $pinboard
     * @param $buildingIds
     * @param $quarterIds
     * @param $users
     */
    protected function saveAnnouncementEmailReceptionists($pinboard, $buildingIds, $quarterIds, $users)
    {
        $residents = Resident::whereIn('user_id', $users->pluck('id'))
            ->select('id')
            ->where('user_id', '!=', $pinboard->user_id)
            ->where('residents.status', Resident::StatusActive)
            ->whereNull('residents.deleted_at')
            ->with([
                'relations' => function ($q) use ($buildingIds, $quarterIds) {
                    $this->setNeededFiltersInQuery($q, $buildingIds, $quarterIds);
                    $q->select('resident_id', 'building_id')
                        ->with('building:id,quarter_id');
                }
            ])
            ->get();

        $data = [];
        $residents->map(function ($resident) use ($buildingIds, $quarterIds, &$data) {
            foreach ($resident->relations as $relation) {
                $building = $relation->building;
                if (empty($building)) {
                    // this case must be not happen in reality
                    continue;
                }

                if (in_array($building->quarter_id, $quarterIds)) {
                    // priority is quarter then building
                    $data['quarters'][$building->quarter_id][] = $resident->id;
                    continue;
                }

                if (in_array($building->id, $buildingIds)) {
                    // priority is quarter then building
                    $data['buildings'][$building->id][] = $resident->id;
                    continue;
                }

                dd('something is not correct must be look');
            }
        });

        if ($data) {
            $pinboard->announcement_email_receptionists()->create([
                'residents_data' => $data,
                'failed_resident_ids' => []
            ]);
        }
    }

    /**
     * @param Pinboard $pinboard
     * @return Collection
     */
    protected function getNotifiedResidentUsers(Pinboard $pinboard, $quarterIds = null, $buildingIds = null)
    {
        if ($pinboard->visibility == Pinboard::VisibilityAll) {
            return User::whereHas('resident', function ($q) {
                    $q->whereNull('residents.deleted_at');
                })
                ->where('id', '!=', $pinboard->user_id) // not sent notification pinboard author
                ->get();
        }

        if (is_null($quarterIds)) {
            $quarterIds = [];
            if ($pinboard->visibility == Pinboard::VisibilityQuarter || $pinboard->announcement) {
                $quarterIds = $pinboard->quarters()->pluck('id')->toArray();
            }
        }

        if (is_null($buildingIds)) {
            $buildingIds = [];
            if ($pinboard->visibility == Pinboard::VisibilityAddress  || $pinboard->announcement) {
                $buildingIds = $pinboard->buildings()->pluck('id')->toArray();
            }
        }

        if (empty($quarterIds) && empty($buildingIds)) {
            return $pinboard->newCollection();
        }

        return User::whereHas('resident', function ($q) use ($quarterIds, $buildingIds, $pinboard) {
            $q->whereNull('residents.deleted_at')
                ->where('id', '!=', $pinboard->user_id) // not sent notification pinboard author
                ->where('residents.status', Resident::StatusActive)
                ->whereHas('relations', function ($q) use ($quarterIds, $buildingIds) {
                    $this->setNeededFiltersInQuery($q, $buildingIds, $quarterIds);
                });
        })->get();
    }

    /**
     * @param $query
     * @param $buildingIds
     * @param $quarterIds
     */
    public function setNeededFiltersInQuery($query, $buildingIds, $quarterIds)
    {
        $query->where('status', Relation::StatusActive)
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
    }
}
