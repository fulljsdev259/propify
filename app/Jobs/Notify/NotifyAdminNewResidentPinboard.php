<?php

namespace App\Jobs\Notify;

use App\Models\Pinboard;
use App\Models\Settings;
use App\Notifications\NewResidentPinboard;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

/**
 * Class NotifyAdminNewResidentPinboard
 * @package App\Jobs\Notify
 */
class NotifyAdminNewResidentPinboard
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

            $notify = (new NewResidentPinboard($pinboard, $admin))->delay(now()->addSeconds($delay));
            $admin->notify($notify);
        }

        $notificationsData = collect([$newResidentPinboard => $admins]);

        if ($this->saveSystemAudit) {
            $pinboard->newSystemNotificationAudit($notificationsData);
        }
        return $notificationsData;
    }
}
