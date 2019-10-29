<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FillOldAuditsFormatCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \OwenIt\Auditing\Models\Audit::get(['id', 'auditable_id', 'auditable_type'])->each(function ($audit) {
            $class = \Chelout\RelationshipEvents\MorphedByMany::$morphMap[$audit->auditable_type] ?? '';
            if ($class) {
                $item = $class::find($audit->auditable_id);
                if (method_exists($item, 'getFormatColumnName')) {
                    $audit->auditable_format = $item->{$item->getFormatColumnName()};
                    $audit->save();
                };
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
