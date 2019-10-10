<?php

namespace App\Mails;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class RequestAdded extends Mailable
{
    use Queueable, SerializesModels;

    public $request;

    /**
     * Create a new message instance.
     *
     * @param Request $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.requestAdded')
            ->subject(__("A new request was added"))
            ->with([
                'resident' => $this->request->resident,
                'lang' => $this->request->resident->settings->language ?? App::getLocale()
            ]);
    }
}
