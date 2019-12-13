<?php

use App\Models\ServiceProvider;
use App\Models\Request;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class RequestsTableSeeder extends Seeder
{
    use  \Traits\TimeTrait;
    var $faker;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->faker = Faker::create();
        if (! App::environment('local')) {
            return;
        }

        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'administrator');
        })->get();

        $requests = [];
        for ($i = 0; $i < 500; $i++) {
            $date = $this->getRandomTime();
            $data = factory(App\Models\Request::class)->make()->toArray();
            $data = array_merge($data, $this->getDateColumns($date));

            if (Request::StatusDone == $data['status']) {
                $data['resolution_time'] = $data['updated_at']->diffInSeconds($data['created_at']);
                $data['solved_date'] = $data['updated_at'];
            }

            $requests[] = App\Models\Request::create($data);
        }

        $user = App\Models\User::where('email', 'resident@example.com')->first();

        $allProviders = ServiceProvider::get(['id', 'user_id']);
        $allManagers = \App\Models\PropertyManager::get(['id', 'user_id']);
        foreach ($requests as $key => $request) {
            $this->addRequestComments($request);
            if ($key < 3) {
                continue;
            }

            $request->resident_id = $user->resident->id;
            $request->status = array_rand(Request::Status);
            if ($request->status == Request::StatusDone) {
                $request->solved_date = now();
                $request->resolution_time = now()->diffInSeconds($request->created_at);

            }
            $providers = $allProviders->random(2);
            $managers = $allManagers->random(2);

            $request->save();
            foreach ($providers as $p) {
                $request->providers()->sync([$p->id => [
                    'created_at' => now(),
                    'user_id' => $p->user_id,
                    'type' => array_rand(\App\Models\RequestAssignee::Type)
                ]]);
            }

            foreach ($managers as $m) {
                $request->managers()->sync([
                    $m->id => [
                        'created_at' => now(),
                        'user_id' => $m->user_id,
                        'type' => array_rand(\App\Models\RequestAssignee::Type)
                    ]]);
            }
        }
    }

    private function addRequestComments(Request $request)
    {
        $totalComments = $this->faker->numberBetween(1, 2);
        $users = [
            $request->resident->user,
        ];

        if ($request->agent) {
            $users [] = $request->agent;
        }

        for ($i = 0; $i < $totalComments; $i++) {
            $user = $users[rand(0, count($users) - 1)];
            $request->commentAsUser($user, $this->faker->sentence(3), null);
        }

        DB::statement("UPDATE comments SET created_at = NOW() + INTERVAL -1 week + INTERVAL id second, updated_at = NOW() + INTERVAL -1 week + INTERVAL id second;");
    }
}
