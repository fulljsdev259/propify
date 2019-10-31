<?php

namespace App\Mails;

use App\Models\Request;
use App\Models\User;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class NewRequestForReceptionist extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var
     */
    protected $user;

    /**
     * NewRequestForReceptionist constructor.
     * @param User $user
     * @param Request $request
     */
    public function __construct(User $user, Request $request)
    {
        $this->request = $request;
        $this->user = $user;
    }

    /**
     * @param TemplateRepository $templateRepository
     * @return NewRequestForReceptionist
     */
    public function build(TemplateRepository $templateRepository)
    {
        $data = $templateRepository->getRequestEmailReceiverTemplate($this->user, $this->request);

        return $this->view('mails.requestEmailReceiver')
            ->subject(__("A new request was added"))
            ->with($data);
    }
}
