<?php

namespace App\Jobs\Notify;

use App\Models\Comment;
use App\Models\Request;
use App\Notifications\RequestCommented;
use App\Notifications\RequestDue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class NotifyRequestNewComment
 * @package App\Jobs\Notify
 */
class NotifyRequestNewComment
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Comment
     */
    private $comment;

    /**
     * @var bool
     */
    private $saveSystemAudit;

    /**
     * NotifyRequestNewComment constructor.
     * @param Request $request
     * @param Comment $comment
     * @param bool $saveSystemAudit
     */
    public function __construct(Request $request, Comment $comment, $saveSystemAudit = true)
    {
        $this->request = $request;
        $this->comment = $comment;
        $this->saveSystemAudit = $saveSystemAudit;
    }

    /**
     * @return \Illuminate\Support\Collection
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function handle()
    {
        $i = 0;
        $users  = collect($this->request->allPeople);
        foreach ($users as $person) {
            $delay = $i++ * env("DELAY_BETWEEN_EMAILS", 10);

            if ($person->id != $this->comment->user->id) {
                $person->notify((new RequestCommented($this->request, $person, $this->comment))
                    ->delay(now()->addSeconds($delay)));
            }
        }


        $requestCommented = get_morph_type_of(RequestCommented::class);
        $notificationsData = collect([$requestCommented => $users]);

        if ($this->saveSystemAudit) {
            $this->request->newSystemNotificationAudit($notificationsData);
        }

        return $notificationsData;
    }
}
