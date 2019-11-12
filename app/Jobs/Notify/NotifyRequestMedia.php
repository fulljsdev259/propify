<?php

namespace App\Jobs\Notify;

use App\Models\Comment;
use App\Models\Media;
use App\Models\Request;
use App\Models\User;
use App\Notifications\RequestCommented;
use App\Notifications\RequestDue;
use App\Notifications\RequestMedia;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class NotifyRequestNewComment
 * @package App\Jobs\Notify
 */
class NotifyRequestMedia
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Media
     */
    private $media;


    /**
     * @var User
     */
    private $uploader;

    /**
     * @var bool
     */
    private $saveSystemAudit;

    /**
     * NotifyRequestMedia constructor.
     * @param Request $request
     * @param Media $media
     * @param User $uploader
     * @param bool $saveSystemAudit
     */
    public function __construct(Request $request, Media $media, User $uploader, $saveSystemAudit = true)
    {
        $this->request = $request;
        $this->media = $media;
        $this->uploader = $uploader;
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

            if ($person->id != $this->uploader->id) {
                $person->notify((new RequestMedia($this->request, $this->uploader, $this->media))
                    ->delay(now()->addSeconds($delay)));
            }
        }
        
        $requestMedia = get_morph_type_of(RequestMedia::class);
        $notificationsData = collect([$requestMedia => $users]);

        if ($this->saveSystemAudit) {
            $this->request->newSystemNotificationAudit($notificationsData);
        }

        return $notificationsData;
    }
}
