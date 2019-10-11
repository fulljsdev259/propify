<?php

namespace App\Notifications;

use App\Models\Pinboard;
use App\Models\Resident;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

/**
 * Class PinboardLiked
 * @package App\Notifications
 */
class PinboardLiked extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Pinboard
     */
    protected $pinboard;
    /**
     * @var Resident
     */
    protected $liker;

    /**
     * PinboardLiked constructor.
     * @param Pinboard $pinboard
     * @param Resident $liker
     */
    public function __construct(Pinboard $pinboard, Resident $liker)
    {
        $this->pinboard = $pinboard;
        $this->liker = $liker;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($notifiable->settings && $notifiable->settings->pinboard_notification) {
            return ['database', 'mail'];
        }

        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $tRepo = new TemplateRepository(app());
        $data = $tRepo->getPinboardLikedParsedTemplate($this->pinboard, $this->liker->user);
        $data['userName'] = $notifiable->name;
        $data['lang'] = $notifiable->settings->language ?? App::getLocale();

        return (new MailMessage)
            ->view('mails.pinboardLiked', $data)->subject($data['subject']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'pinboard_id' => $this->pinboard->id,
            'resident' => $this->liker->name,
            'fragment' => Str::limit($this->pinboard->content, 128),
        ];
    }

    /**
     * @param $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return $this->toArray($notifiable);
    }
}
