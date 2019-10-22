<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CorrectCategoryValueInRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\App\Models\Request::find('4')) {
            \App\Models\Request::where('category', 1)->update(['category' => 16]);
            \App\Models\Request::where('category', 3)->update(['category' => 1]);
            \App\Models\Request::where('category', 4)->update(['category' => 1]);
            \App\Models\Request::where('category', 5)->update(['category' => 1]);
            \App\Models\Request::where('category', 6)->update(['category' => 1]);
            \App\Models\Request::where('category', 13)->update(['category' => 1]);
            \App\Models\Request::where('category', 15)->update(['category' => 1]);
            \App\Models\Request::where('category', 7)->update(['category' => 3, 'sub_category' => 4]);
            \App\Models\Request::where('category', 8)->update(['category' => 3, 'sub_category' => 5]);
            \App\Models\Request::where('category', 9)->update(['category' => 3, 'sub_category' => 6]);
            \App\Models\Request::where('category', 16)->update(['category' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
