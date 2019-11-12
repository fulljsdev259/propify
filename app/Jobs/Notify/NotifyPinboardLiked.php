<?php

namespace App\Jobs\Notify;

use App\Models\Pinboard;
use App\Models\User;
use App\Notifications\PinboardLiked;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class NotifyPinboardLiked
 * @package App\Jobs\Notify
 */
class NotifyPinboardLiked
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Pinboard
     */
    private $pinboard;

    /**
     * @var User
     */
    private $user;

    /**
     * @var bool
     */
    private $saveSystemAudit;

    /**
     * NotifyPinboardLiked constructor.
     * @param Pinboard $pinboard
     * @param User $user
     * @param bool $saveSystemAudit
     */
    public function __construct(Pinboard $pinboard, User $user, $saveSystemAudit = true)
    {
        $this->pinboard = $pinboard;
        $this->user = $user;
        $this->saveSystemAudit = $saveSystemAudit;
    }

    /**
     * @return \Illuminate\Support\Collection
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function handle()
    {
        // if logged in user is resident and
        // author of pinboard is resident and
        // author of pinboard is different than liker
        if (! ($this->user->resident && $this->pinboard->user->resident && $this->user->id != $this->pinboard->user_id)) {
            return collect();
        }
        $this->pinboard->user->notify(new PinboardLiked($this->pinboard, $this->user->resident));


        $pinboardLiked = get_morph_type_of(PinboardLiked::class);
        $notificationsData = collect([$pinboardLiked => $this->user]);

        if ($this->saveSystemAudit) {
            $this->pinboard->newSystemNotificationAudit($notificationsData);
        }

        return $notificationsData;
    }
}
