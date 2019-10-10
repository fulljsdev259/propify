<?php

namespace App\Notifications;

use App\Models\Listing;
use App\Models\Resident;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class ListingLiked extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Listing
     */
    protected $listing;

    /**
     * @var Resident
     */
    protected $liker;

    /**
     * ListingLiked constructor.
     * @param Listing $listing
     * @param Resident $liker
     */
    public function __construct(Listing $listing, Resident $liker)
    {
        $this->listing = $listing;
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
        $data = $tRepo->getListingLikedParsedTemplate($this->listing, $this->liker->user);
        $data['userName'] = $notifiable->name;
        $data['lang'] = $notifiable->settings->language ?? App::getLocale();

        return (new MailMessage)
            ->view('mails.listingLiked', $data)->subject($data['subject']);
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
            'resident' => $this->liker->name,
            'fragment' => Str::limit($this->listing->content, 128),
        ];
    }

    public function toDatabase($notifiable)
    {
        return $this->toArray($notifiable);
    }
}
