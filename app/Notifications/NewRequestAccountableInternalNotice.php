<?php

namespace App\Notifications;

use App\Models\InternalNotice;
use App\Models\User;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\App;

/**
 * Class NewRequestAccountableInternalNotice
 * @package App\Notifications
 */
class NewRequestAccountableInternalNotice extends Notification implements ShouldQueue
{
    use Queueable, InteractsWithQueue;

    public $tries = 3;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var InternalNotice
     */
    protected $internalNotice;

    /**
     * NewAdmin constructor.
     * @param User $user
     * @param User $subjectUser
     */
    public function __construct(User $user, InternalNotice $internalNotice)
    {
        $this->user = $user;
        $this->internalNotice = $internalNotice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $tRepo = new TemplateRepository(app());
        $data = $tRepo->getNewInternalNoticeTemplate($this->user, $this->internalNotice);
        $data['lang'] = $notifiable->settings->language ?? App::getLocale();

        return (new MailMessage)
            ->view('mails.general', $data)
            ->subject($data['subject']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
