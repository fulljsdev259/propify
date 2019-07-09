<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\Tenant;
use App\Repositories\TemplateRepository;
use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class PostCommented extends Notification implements ShouldQueue
{
    use Queueable;

    protected $post;
    protected $commenter;
    protected $comment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Post $post, Tenant $commenter, Comment $comment)
    {
        $this->post = $post;
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
        if ($notifiable->settings && $notifiable->settings->news_notification) {
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
        $msg = $tRepo->getPostCommentedParsedTemplate($this->post, $this->commenter->user, $this->comment);
        return (new MailMessage)
            ->view('mails.postCommented', [
                'body' => $msg['body'],
                'subject' => $msg['subject'],
            ])->subject($msg['subject']);
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
            'post_id' => $this->post->id,
            'tenant' => $this->commenter->name,
            'comment' => $this->comment->comment,
            'fragment' => Str::limit($this->post->content, 128),
        ];
    }

    public function toDatabase($notifiable)
    {
        return $this->toArray($notifiable);
    }
}
