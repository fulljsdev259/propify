<?php

use App\Models\Building;
use Illuminate\Database\Seeder;

class UnitsTableSeeder extends Seeder
{
    use \Traits\TimeTrait;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment('local')) {
            $count = 0;

            $buildings = Building::inRandomOrder()->get(['id', 'created_at', 'quarter_id', 'attic', 'basement']);
            foreach ($buildings as $building) {

                for ($i = 1; $i <= random_int(1, 4); $i++) {
                    if ($count >= 200) {
                        break 2;
                    }
                    $count++;

                    $attr = [
                        'name' => sprintf('B%s - Unit %s', $building->id, $i),
                    ];
                    if ($building->quarter_id) {
                        if (rand(1, 2) - 1) {
                            $attr['quarter_id'] = $building->quarter_id;
                        } else {
                            $attr['building_id'] = $building->id;
                        }
                    } else {
                        $attr['building_id'] = $building->id;
                        $attr['attic'] = $building->attic ? 1 : 0;
                        $attr['basement'] = $building->basement ? 1 : 0;
                    }

                    $date = $this->getRandomTime($building->created_at);
                    $attr = array_merge($attr, $this->getDateColumns($date));
                    factory(App\Models\Unit::class)->create($attr);
                }
            }
        }
    }
}
