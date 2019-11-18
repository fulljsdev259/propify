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
    protected $signature = 'fix-formats {--table=}';

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
        $permittedTables = [
            'quarters',
            'requests',
            'service_providers',
            'residents',
            'buildings',
            'units',
            'property_managers'
        ];
        $tables = $this->option('table') ?? implode(',', $permittedTables);
        $tables = rtrim($tables, ',');
        $tables = explode(',', $tables);


        $diff = array_diff($tables, $permittedTables);
        if ($diff) {
            echo sprintf(
                'This [%s] table is not permitted> Permitted only this [%s] tables',
                implode(', ', $diff),
                implode(', ', $permittedTables)
            );
            return ;
        }

        dump('start correct _formats of tables');
        if (in_array('quarters', $tables)) {
            Quarter::whereNull('internal_quarter_id')->get(['id'])->each(function (Quarter $quarter) {
                $id = $quarter->id;
                $len = strlen($id);
                if ($len < 4) {
                    for ($i = 0; $i < (4 - $len); $i++) {
                        $id = '0' . $id;
                    }
                }
                $quarter->internal_quarter_id  = $id;
                $quarter->save();
            });

            Quarter::get(['id', 'created_at'])->each(function (Quarter $quarter) {
                $quarter->quarter_format  = $quarter->getUniqueIDFormat($quarter->id);
                $quarter->save();
            });
        }

        if (in_array('service_providers', $tables)) {
            ServiceProvider::get(['id', 'created_at'])->each(function (ServiceProvider $serviceProvider) {
                $serviceProvider->service_provider_format  = $serviceProvider->getUniqueIDFormat($serviceProvider->id);
                $serviceProvider->save();
            });
        }

        if (in_array('residents', $tables)) {
            Resident::get(['id', 'created_at'])->each(function (Resident $resident) {
                $resident->resident_format  = $resident->getUniqueIDFormat($resident->id);
                $resident->save();
            });
        }

        if (in_array('buildings', $tables)) {
            Building::get(['id', 'created_at'])->each(function (Building $building) {
                $building->building_format = $building->getUniqueIDFormat($building->id);
                $building->save();
            });
        }

        if (in_array('units', $tables)) {
            Unit::get(['id', 'created_at'])->each(function (Unit $unit) {
                $unit->unit_format  = $unit->getUniqueIDFormat($unit->id);
                $unit->save();
            });
        }

        if (in_array('property_managers', $tables)) {
            PropertyManager::get(['id', 'created_at'])->each(function (PropertyManager $propertyManager) {
                $propertyManager->property_manager_format  = $propertyManager->getUniqueIDFormat($propertyManager->id);
                $propertyManager->save();
            });
        }

        if (in_array('requests', $tables)) {
            Request::get(['id', 'resident_id', 'relation_id', 'created_at'])->each(function (Request $request) {
                $request->request_format  = $request->getUniqueIDFormat($request->id);
                $request->save();
            });
        }

        echo sprintf('[%s] tables _formats correct successfully', implode(', ', $tables)) . PHP_EOL;
    }
}
