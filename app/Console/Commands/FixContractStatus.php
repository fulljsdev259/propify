<?php

namespace App\Console\Commands;

use App\Models\AuditableModel;
use App\Models\Contract;
use App\Models\Resident;
use Illuminate\Console\Command;

class FixContractStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix-contract-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make contract inactive after expiration';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \OwenIt\Auditing\Exceptions\AuditingException
     */
    public function handle()
    {
        $auditData = $this->makeActiveContractToInActiveIfNeed();
        $auditData = $this->makeInactiveContractToActiveIfNeed($auditData);

        (new AuditableModel())->newSystemAudit(
            AuditableModel::MergeInMainData,
            $auditData,
            AuditableModel::EventUpdated,
            true,
            ['command' => $this->signature]
        );
    }

    /**
     *
     */
    protected function makeActiveContractToInActiveIfNeed()
    {
        $auditData = [];
        // get all expired contract
        $contracts = Contract::where('status', Contract::StatusActive)
            ->where('duration', Contract::DurationLimited)
            ->where('end_date', '<', now()->format('Y-m-d'))
            ->get(['id', 'resident_id']);

        $contractIds = $contracts->pluck('id')->all();
        if (empty($contractIds)) {
            return $auditData;
        }

        $auditData['contracts'][] = [
            'ids' => $contractIds,
            'status' => [
                'old' => Contract::StatusActive,
                'new' => Contract::StatusInActive,
            ]
        ];

        // make inactive expired contracts
        Contract::whereIn('id', $contractIds)->update(['status' => Contract::StatusInActive]);
        $residentIds = $contracts->pluck('resident_id')->unique()->toArray();

        if (empty($residentIds)) {
            return $auditData;
        }

        // make inactive residents how all contracts expired
        $residents = Resident::whereIn('id', $residentIds)
            ->where('status', Resident::StatusActive)
            ->whereDoesntHave('contracts', function ($q) {
                $q->where('status', Contract::StatusActive);
            })->get(['id', 'status']);

        $activeResidentIds = $residents->pluck('id')->all();
        if (empty($activeResidentIds)) {
            return $auditData;
        }


        $auditData['residents'][] = [
            'ids' => $activeResidentIds,
            'status' => [
                'old' => Resident::StatusActive,
                'new' => Resident::StatusInActive,
            ]
        ];
        Resident::whereIn('id', $activeResidentIds)->update(['status' => Resident::StatusInActive]);
        return $auditData;
    }

    /**
     *
     */
    protected function makeInactiveContractToActiveIfNeed($auditData)
    {
        // get all not active contract started today
        $contracts = Contract::where('status', Contract::StatusInActive)
            ->where('start_date', '=', now()->format('Y-m-d'))
            ->get(['id', 'resident_id']);

        $contractIds = $contracts->pluck('id')->all();

        if (empty($contractIds)) {
            return $auditData;
        }

        // make  contracts active
        Contract::whereIn('id', $contractIds)->update(['status' => Contract::StatusActive]);
        $auditData['contracts'][] = [
            'ids' => $contractIds,
            'status' => [
                'old' => Contract::StatusInActive,
                'new' => Contract::StatusActive,
            ]
        ];


        $residentIds = $contracts->pluck('resident_id')->unique()->toArray();
        if (empty($residentIds)) {
            return $auditData;
        }


        // make  inactive resident to active
        $residents = Resident::whereIn('id', $residentIds)
            ->where('status', Resident::StatusInActive)
            ->get(['id']);

        $notActiveResidentIds = $residents->pluck('id')->all();
        if (empty($notActiveResidentIds)) {
            return $auditData;
        }

        $auditData['residents'][] = [
            'ids' => $notActiveResidentIds,
            'status' => [
                'old' => Resident::StatusInActive,
                'new' => Resident::StatusActive,
            ]
        ];
        Resident::whereIn('id', $notActiveResidentIds)->update(['status' => Resident::StatusActive]);
        return $auditData;
    }

}
