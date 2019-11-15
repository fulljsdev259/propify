<?php

namespace App\Jobs\Notify;

use App\Models\Contract;
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
        $usersToNotify = $this->getNotifiedResidentUsers($pinboard);
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
        if ($announcementPinboardPublishedUsers->isNotEmpty()) {
            $pinboard->announcement_email_receptionists()->create([
                'resident_ids' => $announcementPinboardPublishedUsers->pluck('resident.id'),
                'failed_resident_ids' => []
            ]);
        }

        if ($this->saveSystemAudit) {
            $pinboard->newSystemNotificationAudit($notificationsData);
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
                ->where('id', '!=', $pinboard->user_id) // not sent notification pinboard author
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

        return User::whereHas('resident', function ($q) use ($quarterIds, $buildingIds, $pinboard) {
            $q->whereNull('residents.deleted_at')
                ->where('id', '!=', $pinboard->user_id) // not sent notification pinboard author
                ->where('residents.status', Resident::StatusActive)
                ->whereHas('contracts', function ($q) use ($quarterIds, $buildingIds) {
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
        $query->where('status', Contract::StatusActive)
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
