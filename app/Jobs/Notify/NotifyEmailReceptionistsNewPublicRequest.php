<?php

namespace App\Jobs\Notify;

use App\Mails\NewRequestForReceptionist;
use App\Models\PropertyManager;
use App\Models\Request;
use App\Models\Settings;
use App\Models\User;
use App\Models\Workflow;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * Class NewAdminNotification
 * @package App\Jobs
 */
class NotifyEmailReceptionistsNewPublicRequest
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private $request;

    /**
     * @var
     */
    private $saveSystemAudit;

    /**
     * NotifyEmailReceptionistsNewPublicRequest constructor.
     * @param Request $request
     * @param bool $saveSystemAudit
     */
    public function __construct(Request $request, $saveSystemAudit = true)
    {
        $this->request = $request;
        $this->saveSystemAudit = $saveSystemAudit;
    }

    /**
     * @return \Illuminate\Support\Collection
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function handle()
    {
        $this->request->load('relation:id,building_id,resident_id', 'relation.building:id,quarter_id,global_email_receptionist');
        $building = $this->request->relation->building ?? null;

        if (empty($building)) {
            return collect();
        }
        $workflows = Workflow::where('category_id', $this->request->category_id)
            ->where('quarter_id', $building->quarter_id)
            ->where('building_ids', 'like', '%' . $building->id .'%')
            ->get();

        $workflows = $workflows->filter(function ($workflow) use ($building) {
            return in_array($building->id, $workflow->building_ids);
        });

        $toUserIds = $workflows->pluck('to_user_ids');
        $ccUserIds = $workflows->pluck('cc_user_ids');
        $userIds = $toUserIds->merge($ccUserIds)
            ->collapse()
            ->unique();
        if ($userIds->isEmpty()) {
            return collect();
        }

        $users = User::whereIn('id', $userIds)->get();

        foreach ($workflows as $workflow) {
            $toUsers = $users->whereIn('id', $workflow->to_user_ids);
            $ccUsers = $users->whereIn('id', $workflow->cc_user_ids);
            $mail = (new NewRequestForReceptionist($this->request, $workflow, $toUsers, $ccUsers))->onQueue('high');
            Mail::to($toUsers)->cc($ccUsers)->queue($mail);
        }

        // @TODO maybe other format save audit
        $newRequestForReceptionist = get_morph_type_of(NewRequestForReceptionist::class);
        $notificationsData = collect([$newRequestForReceptionist => $users]);
        if ($this->saveSystemAudit) {
            $this->request->newSystemNotificationAudit($notificationsData);
        }

        return $notificationsData;
    }

    /**
     * @param $building
     * @param $category
     * @return \Illuminate\Support\Collection|mixed
     */
    protected function getReceptionistUsers($building, $category)
    {
        if (! $building->global_email_receptionist) {
            return $this->getBuildingReceptionistUsers($building, $category);
        }
        $building->load('quarter:id');
        if ($building->quarter) {
            $users = $this->getQuarterReceptionistUsers($building->quarter, $category);
            if ($users->isNotEmpty()) {
                return $users;
            }
        }

        $propertyManagerIds = Settings::first(['email_receptionist_ids'])->email_receptionist_ids ?? [];
        if (empty($propertyManagerIds)) {
            return collect();
        }

        $propertyManagers = PropertyManager::whereIn('id', $propertyManagerIds)
            ->select('id', 'user_id')
            ->with('user:id,name,email')
            ->get();

        return $propertyManagers;
    }

    /**
     * @param $building
     * @param $category
     * @return mixed
     */
    public function getBuildingReceptionistUsers($building, $category)
    {
        $building->load(['email_receptionists' => function ($q) use ($category) {
            $q->select('id', 'model_id', 'property_manager_id')
                ->where('category', $category)
                ->with(['property_manager' => function($q) {
                    $q->select('id', 'user_id')->with('user:id,name,email');
                }]);
        }]);

        return $building->email_receptionists->pluck('property_manager');
    }

    /**
     * @param $quarter
     * @param $category
     * @return \Illuminate\Support\Collection
     */
    public function getQuarterReceptionistUsers($quarter, $category)
    {
        if (empty($quarter)) {
            return collect();
        }

        $quarter->load(['email_receptionists' => function ($q) use ($category) {
            $q->select('id', 'model_id', 'property_manager_id')
                ->where('category', $category)
                ->with(['property_manager' => function($q) {
                    $q->select('id', 'user_id')->with('user:id,name,email');
                }]);
        }]);

        $users = $quarter->email_receptionists->pluck('property_manager');
        return $users->isNotEmpty() ? $users : collect();
    }
}
