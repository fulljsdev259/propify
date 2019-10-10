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
                $u = $pinboard->user;
                if ($u->resident && $u->resident->building) {
                    $pinboard->buildings()->sync($u->resident->building->id);
                    if ($u->resident->building->quarter_id) {
                        $pinboard->quarters()->sync($u->resident->building->quarter_id);
                    }
                }
                //$pRepo->setStatus($pinboard->id, Post::StatusPublished, now());
            }
        }
    }
}
