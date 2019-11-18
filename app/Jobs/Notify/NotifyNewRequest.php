<?php

namespace App\Jobs\Notify;

use App\Models\Relation;
use App\Models\Pinboard;
use App\Models\PropertyManager;
use App\Models\Request;
use App\Models\Resident;
use App\Notifications\NewResidentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Collection;
use App\Models\User;

/**
 * Class NotifyNewRequest
 * @package App\Jobs\Notify
 */
class NotifyNewRequest
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Pinboard
     */
    private $request;

    /**
     * @var bool
     */
    private $saveSystemAudit;

    /**
     * NotifyNewRequest constructor.
     * @param Request $request
     * @param bool $saveSystemAudit
     */
    public function __construct(Request $request, $saveSystemAudit = true)
    {
        $this->request = $request;
        $this->saveSystemAudit = $saveSystemAudit;
    }

    /**
     * @return bool|\Illuminate\Support\Collection
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function handle()
    {
        $request = $this->request;
        $relation = $this->request->relation;
        if (empty($relation->building)) {
            return collect();
        }

        $propertyManagers = PropertyManager::whereHas('buildings', function ($q) use ($relation) {
            $q->where('buildings.id', $relation->building->id);
        })->select('id', 'user_id')->with('user')->get();

        $i = 0;
        foreach ($propertyManagers as $propertyManager) {
            $delay = $i++ * env("DELAY_BETWEEN_EMAILS", 10);
            $propertyManager->user->redirect = "/admin/requests/" . $request->id;

            $propertyManager->user
                ->notify((new NewResidentRequest($request, $propertyManager->user, $relation->resident->user))
                    ->delay(now()->addSeconds($delay)));
        }

        $newResidentPinboard = get_morph_type_of(NewResidentRequest::class);
        $notificationsData = collect([$newResidentPinboard => $propertyManagers->pluck('user')]);
        if ($this->saveSystemAudit) {
            $request->newSystemNotificationAudit($notificationsData);
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
