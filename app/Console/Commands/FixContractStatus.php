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
     * @return mixed
     */
    public function handle()
    {
        $query = Contract::where('status', Contract::StatusInactive)
            ->where('start_date', '=', now()->format('Y-m-d'));

        // get all not active contract until today
        $contracts = $query->get(['resident_id']);

        $residentIds = $contracts->pluck('resident_id')->unique()->toArray();
        if ($residentIds) {
            // make  contracts active
            $query->update(['status' => Contract::StatusActive]);
            // make  inactive resident to active
            Resident::whereIn('id', $residentIds)
                ->where('status', Resident::StatusInActive)
                ->update(['status' => Resident::StatusActive]);
        }

        $query = Contract::where('status', Contract::StatusActive)
            ->where('duration', Contract::DurationLimited)
            ->where('end_date', '<', now()->format('Y-m-d'));

        // get all expired contract
        $contracts = $query->get(['resident_id']);
        // make inactive expired contracts
        $query->update(['status' => Contract::StatusInactive]);

        $residentIds = $contracts->pluck('resident_id')->unique()->toArray();
        // make inactive residents how all contracts expired
        Resident::whereIn('id', $residentIds)->whereDoesntHave('contracts', function ($q) {
            $q->where('status', Contract::StatusActive);
        })->update(['status' => Contract::StatusInactive]);
    }
}
