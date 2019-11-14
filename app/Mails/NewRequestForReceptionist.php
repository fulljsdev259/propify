<?php

namespace App\Mails;

use App\Models\Request;
use App\Models\User;
use App\Models\Workflow;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class NewRequestForReceptionist extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var
     */
    protected $workflow;
    protected $toUsers;
    protected $ccUsers;

    /**
     * NewRequestForReceptionist constructor.
     * @param Request $request
     * @param Workflow $workflow
     * @param $toUsers
     * @param $ccUsers
     */
    public function __construct(Request $request, Workflow $workflow, $toUsers, $ccUsers)
    {
        $this->request = $request;
        $this->workflow = $workflow;
    }

    /**
     * @param TemplateRepository $templateRepository
     * @return NewRequestForReceptionist
     */
    public function build(TemplateRepository $templateRepository)
    {
        $data = $templateRepository->getRequestEmailReceiverTemplate($this->request, $this->workflow);

        return $this->view('mails.requestEmailReceiver')
            ->subject(__("A new request was added"))
            ->with($data);
    }
}
