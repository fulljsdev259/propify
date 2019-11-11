<?php

namespace App\Jobs\Notify;

use App\Models\Comment;
use App\Models\Pinboard;
use App\Models\User;
use App\Notifications\PinboardCommented;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class NotifyPinboardNewComment
 * @package App\Jobs\Notify
 */
class NotifyPinboardNewComment
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
     * @var Comment
     */
    private $comment;

    /**
     * @var bool
     */
    private $saveSystemAudit;

    /**
     * NotifyPinboardNewComment constructor.
     * @param Pinboard $pinboard
     * @param Comment $comment
     * @param User $user
     * @param bool $saveSystemAudit
     */
    public function __construct(Pinboard $pinboard, Comment $comment, User $user, $saveSystemAudit = true)
    {
        $this->pinboard = $pinboard;
        $this->user = $user;
        $this->comment = $comment;
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
        // author of pinboard is different than commenter
        if (! ($this->user->resident && $this->pinboard->user->resident && $this->user->id != $this->pinboard->user_id)) {
            return collect();
        }

        $this->pinboard->user->notify(new PinboardCommented($this->pinboard, $this->user->resident, $this->comment));
        $pinboardCommented = get_morph_type_of(PinboardCommented::class);
        $notificationsData = collect([$pinboardCommented => $this->user]);

        if ($this->saveSystemAudit) {
            $this->pinboard->newSystemNotificationAudit($notificationsData);
        }

        return $notificationsData;
    }
}
