<?php

namespace App\Notifications;

use App\Models\Pinboard;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

/**
 * Class AnnouncementPinboardPublished
 * @package App\Notifications
 */
class AnnouncementPinboardPublished extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Pinboard
     */
    protected $pinboard;

    /**
     * AnnouncementPinboardPublished constructor.
     * @param Pinboard $pinboard
     */
    public function __construct(Pinboard $pinboard)
    {
        $this->pinboard = $pinboard;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
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
        $data = $tRepo->getAnnouncementPinboardParsedTemplate($this->pinboard, $notifiable);
        $data['userName'] = $notifiable->name;
        $data['lang'] = $notifiable->settings->language ?? App::getLocale();

        return (new MailMessage)->view('mails.announcementPinboardPublished', $data)->subject($data['subject']);
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
            'pinboard_type' => $this->pinboard->type,
            'user_name' => $this->pinboard->user->name,
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
