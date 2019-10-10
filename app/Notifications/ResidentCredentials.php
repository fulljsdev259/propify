<?php

namespace App\Notifications;

use App\Models\Resident;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;

/**
 * Class ResidentCredentials
 * @package App\Notifications
 */
class ResidentCredentials extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    /**
     * @var Resident
     */
    protected $resident;

    /**
     * ResidentCredentials constructor.
     * @param Resident $resident
     */
    public function __construct(Resident $resident)
    {
        $this->resident = $resident;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $tRepo = new TemplateRepository(app());
        $data = $tRepo->getResidentCredentialsParsedTemplate($this->resident);
        $data['userName'] = $notifiable->name;
        $data['lang'] = $notifiable->settings->language ?? App::getLocale();
        //dump(view('mails.sendResidentCredentials', $data)->render());

        return (new MailMessage)
            ->view('mails.sendResidentCredentials', $data)
            ->subject($data['subject']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [];
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
