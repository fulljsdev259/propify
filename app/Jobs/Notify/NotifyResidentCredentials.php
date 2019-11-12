<?php

namespace App\Jobs\Notify;

use App\Models\Resident;
use App\Notifications\ResidentCredentials;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class NotifyResidentCredentials
 * @package App\Jobs\Notify
 */
class NotifyResidentCredentials
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Resident
     */
    private $resident;

    /**
     * @var bool
     */
    private $saveSystemAudit;

    /**
     * ResidentNotify constructor.
     * @param Resident $resident
     * @param bool $saveSystemAudit
     */
    public function __construct(Resident $resident, $saveSystemAudit = true)
    {
        $this->resident = $resident;
        $this->saveSystemAudit = $saveSystemAudit;
    }

    /**
     * @return \Illuminate\Support\Collection
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function handle()
    {
        $this->resident->user->notify(new ResidentCredentials($this->resident));

        $residentDue = get_morph_type_of(ResidentCredentials::class);
        $notificationsData = collect([$residentDue => $this->resident->user]);

        if ($this->saveSystemAudit) {
            $this->resident->newSystemNotificationAudit($notificationsData);
        }

        return $notificationsData;
    }
}
