<?php

namespace App\Notifications;

use App\Models\Request;
use App\Models\User;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;

/**
 * Class NewResidentRequest
 * @package App\Notifications
 */
class NewResidentRequest extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Request
     */
    protected $request;
    /**
     * @var string
     */
    protected $user;
    /**
     * @var string
     */
    protected $subjectUser;

    /**
     * NewResidentRequest constructor.
     * @param Request $request
     * @param User $user
     * @param User $subjectUser
     */
    public function __construct(Request $request, User $user, User $subjectUser)
    {
        $this->request = $request;
        $this->user = $user;
        $this->subjectUser = $subjectUser;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
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
        $data = $tRepo->getNewRequestParsedTemplate($this->request, $this->user, $this->subjectUser);
        $data['userName'] = $notifiable->name;
        $data['lang'] = $notifiable->settings->language ?? App::getLocale();

        return (new MailMessage)
            ->view('mails.request', $data)->subject($data['subject']);
    }

    /**
     * @param $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return $this->toArray($notifiable);
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
            'pinboard_id' => $this->request->id, // @TODO correct it but be pinboard_id or service_request_id
            'user_name' => $this->request->resident->user->name,
            'fragment' => 'New request opened',
        ];
    }
}
