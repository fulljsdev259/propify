<?php

use App\Models\Template;
use App\Models\TemplateCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Class TranslationTableSeeder
 */
class TranslationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::unprepared(file_get_contents(database_path('sql' . DIRECTORY_SEPARATOR . 'translations.sql')));
        Schema::enableForeignKeyConstraints();
        return;
    }
}
