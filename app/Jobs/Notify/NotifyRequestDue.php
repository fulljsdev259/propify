<?php

namespace App\Jobs\Notify;

use App\Models\Request;
use App\Notifications\RequestDue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class NotifyRequestDue
 * @package App\Jobs\Notify
 */
class NotifyRequestDue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var bool
     */
    private $saveSystemAudit;

    /**
     * RequestNotify constructor.
     * @param Request $request
     * @param bool $saveSystemAudit
     */
    public function __construct(Request $request, $saveSystemAudit = true)
    {
        $this->request = $request;
        $this->saveSystemAudit = $saveSystemAudit;
    }

    /**
     * @return \Illuminate\Support\Collection
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function handle()
    {
        $request = $this->request;
        $beforeHours = env('REQUEST_DUE_MAIL', 24);
        $providerUsers = $request->providers()->with('user')->get()->pluck('user');
        $managerUsers = $request->managers()->with('user')->get()->pluck('user');
        $users = $providerUsers->merge($managerUsers);

        foreach ($users as $user) {
            $user->notify((new RequestDue($request))->delay($request->due_date->subHours($beforeHours)));
        }

        $requestDue = get_morph_type_of(RequestDue::class);
        $notificationsData = collect([$requestDue => $users]);

        if ($this->saveSystemAudit) {
            $request->newSystemNotificationAudit($notificationsData);
        }

        return $notificationsData;
    }
}
