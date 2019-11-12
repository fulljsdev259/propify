<?php

namespace App\Jobs\Notify;

use App\Mails\NotifyServiceProvider;
use App\Models\AuditableModel;
use App\Models\PropertyManager;
use App\Models\Request;
use App\Models\ServiceProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class NotifyRequestProvider
 * @package App\Jobs\Notify
 */
class NotifyRequestProvider
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ServiceProvider
     */
    private $serviceProvider;

    /**
     * @var PropertyManager
     */
    private $propertyManager;

    /**
     * @var array
     */
    private $mailDetails;

    /**
     * @var bool
     */
    private $saveSystemAudit;

    /**
     * NotifyRequestProvider constructor.
     * @param Request $request
     * @param ServiceProvider $serviceProvider
     * @param $propertyManager
     * @param $mailDetails
     * @param bool $saveSystemAudit
     */
    public function __construct(Request $request, ServiceProvider $serviceProvider, $propertyManager, $mailDetails, $saveSystemAudit = true)
    {
        $this->request = $request;
        $this->serviceProvider = $serviceProvider;
        $this->propertyManager = $propertyManager;
        $this->mailDetails = $mailDetails;
        $this->saveSystemAudit = $saveSystemAudit;
    }

    /**
     * @return \Illuminate\Support\Collection
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function handle()
    {
        $propertyManager = $this->propertyManager;
        $serviceProvider = $this->serviceProvider;
        $request = $this->request;
        $mailDetails = $this->mailDetails;

        $toEmails = [$this->serviceProvider->user->email];
        if (!empty($mailDetails['to'])) {
            $toEmails[] = $mailDetails['to'];
        }

        $ccEmails = $propertyManager ? [$propertyManager->user->email] : [];
        if (!empty($mailDetails['cc']) && is_array($mailDetails['cc'])) {
            $ccEmails = array_merge($ccEmails, $mailDetails['cc']);
        }

        $bccEmails = $mailDetails['bcc'] ?? [];
        \Mail::to($toEmails)
            ->cc($ccEmails)
            ->bcc($bccEmails)
            ->queue( new NotifyServiceProvider($serviceProvider, $request, $mailDetails));

        $auditData = compact('serviceProvider', 'propertyManager', 'mailDetails');
        $request->registerAuditEvent(AuditableModel::EventProviderNotified, $auditData);

        $u = \Auth::user();

        // @TODO new system audit for conversation
        $conversation = $request->conversationFor($u, $serviceProvider->user);
        $comment = $mailDetails['title'] . "\n\n" . strip_tags($mailDetails['body']);
        $conversation->comment($comment);

        if ($propertyManager && $propertyManager->user) {
            $conversation = $request->conversationFor($u, $propertyManager->user);
            if ($conversation) {
                $conversation->comment($comment);
            }
        }

        $request->touch();
        $serviceProvider->touch();

        $users = collect([$serviceProvider->user]);
        if ($propertyManager) {
            $users->push($propertyManager->user);
        }

        $notifyServiceProvider = get_morph_type_of(NotifyServiceProvider::class);
        $notificationsData = collect([$notifyServiceProvider => [
            'users' => $users,
            'to' => $toEmails,
            'cc' => $ccEmails,
            'bcc' => $bccEmails,
        ]]);


        if ($this->saveSystemAudit) {
            $this->request->newSystemNotificationAudit($notificationsData);
        }

        return $notificationsData;
    }
}
