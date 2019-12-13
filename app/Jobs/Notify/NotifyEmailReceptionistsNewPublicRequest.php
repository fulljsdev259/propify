<?php

namespace App\Jobs\Notify;

use App\Mails\NewRequestForReceptionist;
use App\Models\Request;
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

        return;
        $this->request->load('relation:id,building_id,resident_id', 'relation.building:id,quarter_id');
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
}
