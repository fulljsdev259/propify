<?php

namespace App\Jobs\Notify;

use App\Models\Request;
use App\Notifications\StatusChangedRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class NotifyRequestStatusChanges
 * @package App\Jobs\Notify
 */
class NotifyRequestStatusChange
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Request
     */
    private $originalRequest;

    /**
     * @var bool
     */
    private $saveSystemAudit;

    /**
     * NotifyRequestStatusChanges constructor.
     * @param Request $originalRequest
     * @param Request $request
     * @param bool $saveSystemAudit
     */
    public function __construct(Request $originalRequest, Request $request, $saveSystemAudit = true)
    {
        $this->originalRequest = $originalRequest;
        $this->request = $request;
        $this->saveSystemAudit = $saveSystemAudit;
    }

    /**
     * @return \Illuminate\Support\Collection
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function handle()
    {

        if ($this->originalRequest->status == $this->request->status) {
            return collect();
        }

        $user = $this->request->resident->user;
        $user->notify(new StatusChangedRequest($this->request, $this->originalRequest, $user));


        $statusChangedRequest = get_morph_type_of(StatusChangedRequest::class);
        $notificationsData = collect([$statusChangedRequest => $user]);

        if ($this->saveSystemAudit) {
            $this->request->newSystemNotificationAudit($notificationsData);
        }

        return $notificationsData;
    }
}
