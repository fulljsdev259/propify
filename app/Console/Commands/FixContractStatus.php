<?php

namespace App\Console\Commands;

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
     * Execute the console command.
     *
     */
    public function handle()
    {
        $this->makeActiveContractToInActiveIfNeed();
        $this->makeInactiveContractToActiveIfNeed();
    }

    /**
     *
     */
    protected function makeInactiveContractToActiveIfNeed()
    {
        // get all not active contract started today
        $contracts = Contract::where('status', Contract::StatusInactive)
            ->where('start_date', '=', now()->format('Y-m-d'))
            ->get(['id', 'resident_id', 'status']);

        $residentIds = $contracts->pluck('resident_id')->unique()->toArray();
        // make  contracts active
        foreach ($contracts as $contract) {
            $contract->update(['status' => Contract::StatusActive]);
        }

        if ($residentIds) {
            // make  inactive resident to active
            $residents = Resident::whereIn('id', $residentIds)
                ->where('status', Resident::StatusInActive)
                ->get(['id', 'status']);

            foreach ($residents as $resident) {
                $resident->update(['status' => Resident::StatusActive]);
            }
        }
    }

    /**
     *
     */
    protected function makeActiveContractToInActiveIfNeed()
    {
        // get all expired contract
        $contracts = Contract::where('status', Contract::StatusActive)
            ->where('duration', Contract::DurationLimited)
            ->where('end_date', '<', now()->format('Y-m-d'))
            ->get(['id', 'resident_id', 'status']);

        // make inactive expired contracts
        foreach ($contracts as $contract) {
            $contract->update(['status' => Contract::StatusInactive]);
        }

        $residentIds = $contracts->pluck('resident_id')->unique()->toArray();

        if ($residentIds) {
            // make inactive residents how all contracts expired
            $residents = Resident::whereIn('id', $residentIds)
                ->whereDoesntHave('contracts', function ($q) {
                    $q->where('status', Contract::StatusActive);
                })->get(['id', 'status']);
            
            foreach ($residents as $resident) {
                $resident->update(['status' => Resident::StatusActive]);
            }
        }
    }
}
