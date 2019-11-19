<?php

namespace App\Mails;

use App\Models\Request;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class SentEmailPdf extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    
    protected $user;
    protected $request;
    protected $storage_path;
    
    public function __construct(User $user,Request $request,$storage_path=null)
    {
        $this->user=$user;
        $this->request=$request;
        $this->storage_path=$storage_path;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		return $this->view('mails.send_all_pdf')
			->attach(Storage::disk('request_downloads')->path($this->storage_path),[
				'as' => $this->storage_path,
				'mime' => 'application/pdf',
			])
			->subject(__("A new request pdf"));
    }
}
