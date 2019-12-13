<?php

namespace App\Jobs\Notify;

use App\Models\InternalNotice;
use App\Models\RequestAssignee;
use App\Models\User;
use App\Notifications\NewInternalNotice;
use App\Notifications\NewRequestAccountableInternalNotice;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class NotifyNewRequestInternalNotice
 * @package App\Jobs\Notify
 */
class NotifyNewRequestInternalNotice
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var InternalNotice
     */
    private $internalNotice;

    /**
     * @var bool
     */
    private $saveSystemAudit;

    /**
     * NotifyNewRequestInternalNotice constructor.
     * @param InternalNotice $internalNotice
     * @param bool $saveSystemAudit
     */
    public function __construct(InternalNotice $internalNotice, $saveSystemAudit = true)
    {
        $this->internalNotice = $internalNotice;
        $this->saveSystemAudit = $saveSystemAudit;
    }

    /**
     * @return \Illuminate\Support\Collection
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function handle()
    {
        return;
        $requestAssignne = RequestAssignee::where('request_id', $this->internalNotice->request_id)
            ->where('type', RequestAssignee::TypeAccountable)
            ->latest()
            ->select('user_id')
            ->with('user')
            ->first();

        $notificationsData = collect();
        $accountableUser = $requestAssignne->user ?? null; // for prevent when user deleted not throw exception
        if ($accountableUser) {
            if ($accountableUser->id != \Auth::id()) {
                $notify = (new NewRequestAccountableInternalNotice($accountableUser, $this->internalNotice));
                $accountableUser->notify($notify);
                $notification = get_morph_type_of(NewRequestAccountableInternalNotice::class);
                $notificationsData[$notification] = $accountableUser;
            }
        }

        $userIds = $this->internalNotice->user_ids;
        if (empty($userIds)) {
            if ($this->saveSystemAudit && $notificationsData->isNotEmpty()) {
                $this->internalNotice->newSystemNotificationAudit($notificationsData);
            }
            return $notificationsData;
        }

        $users = User::whereIn('id', $userIds)->where('id', '!=', \Auth::id())->get();
        $i = 0;
        foreach ($users as $user) {
            $delay = $i++ * env("DELAY_BETWEEN_EMAILS", 10);
            $notify = (new NewInternalNotice($user, $this->internalNotice))->delay(now()->addSeconds($delay));
            $user->notify($notify);
        }

        $notification = get_morph_type_of(NewInternalNotice::class);
        $notificationsData[$notification] = $users;

        if ($this->saveSystemAudit) {
            $this->internalNotice->newSystemNotificationAudit($notificationsData);
        }

        return $notificationsData;
    }
}
