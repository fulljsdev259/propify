<?php

namespace App\Console\Commands;

use App\Models\Building;
use App\Models\PropertyManager;
use App\Models\Quarter;
use App\Models\ServiceProvider;
use App\Models\Request;
use App\Models\Resident;
use App\Models\Unit;
use Illuminate\Console\Command;

class FixFormats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix-formats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix *_format of tables';

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
        dump('start correct _formats of tables');
        Request::get(['id', 'resident_id', 'contract_id', 'created_at'])->each(function (Request $request) {
            $request->request_format  = $request->getUniqueIDFormat($request->id);
            $request->save();
        });
        ServiceProvider::get(['id', 'created_at'])->each(function (ServiceProvider $serviceProvider) {
            $serviceProvider->service_provider_format  = $serviceProvider->getUniqueIDFormat($serviceProvider->id);
            $serviceProvider->save();
        });
        Resident::get(['id', 'created_at'])->each(function (Resident $resident) {
            $resident->resident_format  = $resident->getUniqueIDFormat($resident->id);
            $resident->save();
        });
        Building::get(['id', 'created_at'])->each(function (Building $building) {
            $building->building_format  = $building->getUniqueIDFormat($building->id);
            $building->save();
        });
        Unit::get(['id', 'created_at'])->each(function (Unit $unit) {
            $unit->unit_format  = $unit->getUniqueIDFormat($unit->id);
            $unit->save();
        });
        Quarter::get(['id', 'created_at'])->each(function (Quarter $quarter) {
            $quarter->quarter_format  = $quarter->getUniqueIDFormat($quarter->id);
            $quarter->save();
        });
        PropertyManager::get(['id', 'created_at'])->each(function (PropertyManager $propertyManager) {
            $propertyManager->property_manager_format  = $propertyManager->getUniqueIDFormat($propertyManager->id);
            $propertyManager->save();
        });
        dump('all _formats correct successfully');
    }
}
