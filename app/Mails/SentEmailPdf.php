<?php

namespace App\Mails;

use App\Models\Request;
use App\Models\User;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class SentEmailPdf extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    
    protected $storage_path;
    protected $user;
    
    public function __construct($user, $storage_path = null)
    {
        $this->storage_path = $storage_path;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $tRepo = new TemplateRepository(app());
        $data = $tRepo->getMassRequestsNotificationServiceProviderTemplate($this->user);
        $data['userName'] = $this->user->name;
        $data['lang'] = $this->user->settings->language ?? App::getLocale();

		return $this->view('mails.general', $data)
			->attach(Storage::disk('request_downloads')->path($this->storage_path),[
				'as' => $this->storage_path,
				'mime' => 'application/pdf',
			])->subject($data['subject']);
    }
}
