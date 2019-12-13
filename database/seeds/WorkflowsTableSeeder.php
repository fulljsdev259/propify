<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class WorkflowsTableSeeder extends Seeder
{
    use \Traits\TimeTrait;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!App::environment('local')) {
            return;
        }

        $quarters = \App\Models\Quarter::has('buildings')->select('id')->with('buildings:id,quarter_id')->get();
        $users = User::has('property_manager')->orHas('service_provider')->get('id');
        foreach ($quarters as $quarter) {
            $this->makeWorkflow($quarter, App\Models\Request::CategoryGeneral, $users);
            $this->makeWorkflow($quarter, App\Models\Request::CategoryMalfunction, $users);
            $this->makeWorkflow($quarter, App\Models\Request::CategoryDeficiency, $users);
            $this->makeWorkflow($quarter, App\Models\Request::CategoryOpenIssue, $users);
        }
    }

    protected function makeWorkflow($quarter, $category, $users)
    {
         factory(App\Models\Workflow::class)->create([
            'quarter_id' => $quarter,
            'category_id' => $category,
             'building_ids' => $quarter->buildings->random(random_int(1, $quarter->buildings->count()))->pluck('id'),
             'to_user_ids' => $users->random(random_int(1, 2))->pluck('id')->all(),
             'cc_user_ids' => $users->random(random_int(0, 2))->pluck('id')->all(),
         ]);

    }
}
