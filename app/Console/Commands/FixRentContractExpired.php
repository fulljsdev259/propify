<?php

namespace App\Console\Commands;

use App\Models\RentContract;
use App\Models\Resident;
use Illuminate\Console\Command;

class FixRentContractExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix-rent-contract-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make rent contract inactive after expiration';

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
        $query = RentContract::where('status', RentContract::StatusActive)
            ->where('duration', RentContract::DurationLimited)
            ->where('end_date', '<', now()->format('Y-m-d'));

        // get all expired rent contract
        $rentContracts = $query->get(['id', 'resident_id']);
        // make inactive expired rent contracts
        $query->update(['status' => RentContract::StatusInactive]);

        $residentIds = $rentContracts->pluck('resident_id')->unique()->toArray();
        // make inactive residents how all rent contracts expired
        Resident::whereIn('id', $residentIds)->whereDoesntHave('rent_contracts', function ($q) {
            $q->where('status', RentContract::StatusActive);
        })->update(['status' => RentContract::StatusInactive]);
    }
}
