<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Pinboard;
use App\Models\Tenant;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

/**
 * Class PinboardCommented
 * @package App\Notifications
 */
class PinboardCommented extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Pinboard
     */
    protected $pinboard;
    /**
     * @var Tenant
     */
    protected $commenter;
    /**
     * @var Comment
     */
    protected $comment;

    /**
     * PinboardCommented constructor.
     * @param Pinboard $pinboard
     * @param Tenant $commenter
     * @param Comment $comment
     */
    public function __construct(Pinboard $pinboard, Tenant $commenter, Comment $comment)
    {
        $this->pinboard = $pinboard;
        $this->commenter = $commenter;
        $this->comment = $comment;
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
        $data = $tRepo->getPinboradCommentedParsedTemplate($this->pinboard, $this->commenter->user, $this->comment);
        $data['userName'] = $notifiable->name;
        $data['lang'] = $notifiable->settings->language ?? App::getLocale();

        return (new MailMessage)
            ->view('mails.pinboardCommented', $data)->subject($data['subject']);
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
            'tenant' => $this->commenter->name,
            'comment' => $this->comment->comment,
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
