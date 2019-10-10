<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Listing;
use App\Models\Resident;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

/**
 * Class ListingCommented
 * @package App\Notifications
 */
class ListingCommented extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Listing
     */
    protected $listing;
    /**
     * @var Resident
     */
    protected $commenter;
    /**
     * @var Comment
     */
    protected $comment;

    /**
     * ListingCommented constructor.
     * @param Listing $listing
     * @param Resident $commenter
     * @param Comment $comment
     */
    public function __construct(Listing $listing, Resident $commenter, Comment $comment)
    {
        $this->listing = $listing;
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
        if ($notifiable->settings && $notifiable->settings->listing_notification) {
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
        $data = $tRepo->getListingCommentedParsedTemplate($this->listing, $this->commenter->user, $this->comment);
        $data['userName'] = $notifiable->name;
        $data['lang'] = $notifiable->settings->language ?? App::getLocale();

        return (new MailMessage)
            ->view('mails.listingCommented', $data)->subject($data['subject']);
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
            'listing_id' => $this->listing->id,
            'resident' => $this->commenter->name,
            'comment' => $this->comment->comment,
            'fragment' => Str::limit($this->listing->title, 128),
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
