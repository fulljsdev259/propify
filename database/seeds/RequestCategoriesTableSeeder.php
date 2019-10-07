<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RequestCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::unprepared(file_get_contents(database_path('sql' . DIRECTORY_SEPARATOR . 'request_categories.sql')));
        Schema::enableForeignKeyConstraints();
    }
}
