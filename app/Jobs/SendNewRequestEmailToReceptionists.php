<?php

namespace App\Jobs;

use App\Mails\NewRequestForReceptionist;
use App\Models\PropertyManager;
use App\Models\Request;
use App\Models\Settings;
use App\Models\User;
use App\Notifications\NewAdmin;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * Class NewAdminNotification
 * @package App\Jobs
 */
class SendNewRequestEmailToReceptionists
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private $request;

    /**
     * SendNewRequstNotificationToEmailReceptionist constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle()
    {
        $this->request->load('contract:id,building_id', 'contract.building:id,quarter_id,global_email_receptionist');
        $building = $this->request->contract->building ?? null;

        if (empty($building)) {
            return;
        }

        $users = $this->getReceptionistUsers($building,  $this->request->category);

        if ($users->isEmpty()) {
            return;
        }

        // @TODO save audit
        foreach ($users as $user) {
            Mail::to($user)
                ->queue(new NewRequestForReceptionist($user, $this->request));
        }

    }

    protected function getReceptionistUsers($building, $category)
    {
        if (! $building->global_email_receptionist) {
            return $this->getBuildingReceptionistUsers($building, $category);
        }

        $building->load('quarter:id');
        if ($building->quarter) {
            $users = $this->getQuarterReceptionistUsers($building->quarter, $category);
            if ($users->isNotEmpty()) {
                return $users;
            }
        }

        $propertyManagerIds = Settings::first(['email_receptionist_ids'])->email_receptionist_ids ?? [];
        if (empty($propertyManagerIds)) {
            return collect();
        }

        $propertyManagers = PropertyManager::whereIn('id', $propertyManagerIds)
            ->select('id', 'user_id')
            ->with('user:id,name,email')
            ->get();

        return $propertyManagers->pluck('user');
    }

    /**
     * @param $building
     * @param $category
     * @return mixed
     */
    public function getBuildingReceptionistUsers($building, $category)
    {
        $building->load(['email_receptionists' => function ($q) use ($category) {
            $q->select('id', 'model_id', 'property_manager_id')
                ->where('category', $category)
                ->with(['property_manager' => function($q) {
                    $q->select('id', 'user_id')->with('user:id,name,email');
                }]);
        }]);
        return $building->email_receptionists->pluck('property_manager.user');
    }

    /**
     * @param $quarter
     * @param $category
     * @return \Illuminate\Support\Collection
     */
    public function getQuarterReceptionistUsers($quarter, $category)
    {
        if (empty($quarter)) {
            return collect();
        }

        $quarter->load(['email_receptionists' => function ($q) use ($category) {
            $q->select('id', 'model_id', 'property_manager_id')
                ->where('category', $category)
                ->with(['property_manager' => function($q) {
                    $q->select('id', 'user_id')->with('user:id,name,email');
                }]);
        }]);

        $users = $quarter->email_receptionists->pluck('property_manager.user');
        return $users->isNotEmpty() ? $users : collect();
    }
}
