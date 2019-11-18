<?php

use App\Models\Pinboard;
use App\Repositories\PinboardRepository;
use Illuminate\Database\Seeder;

class PinboardTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $pRepo = new PostRepository(app());
        if (App::environment('local')) {
            $totalPosts = 200;
            $pinboards = factory(App\Models\Pinboard::class, $totalPosts)->create(['status' => Pinboard::StatusPublished]);
            foreach ($pinboards as $pinboard) {
                $user = $pinboard->user;
                if ($user->resident && ! empty($user->resident->default_relation->building)) {
                    $pinboard->buildings()->sync($user->resident->default_relation->building->id);
                    if ($user->resident->default_relation->building->quarter_id) {
                        $pinboard->quarters()->sync($user->resident->default_relation->building->quarter_id);
                    }
                }
                //$pRepo->setStatus($pinboard->id, Post::StatusPublished, now());
            }
        }
    }
}
