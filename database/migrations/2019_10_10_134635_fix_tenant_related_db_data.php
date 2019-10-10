<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixTenantRelatedDbData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        update_db_fields(\App\Models\Role::class, 'name', 'tenant', 'resident');
        update_db_fields(\App\Models\Permission::class, ['name', 'description', 'display_name'], 'tenant', 'resident');
        update_db_fields(\App\Models\Template::class, ['name', 'subject', 'body'], 'tenant', 'resident');
        update_db_fields(\App\Models\TemplateCategory::class, ['name', 'description', 'subject', 'body'], 'tenant', 'resident');
        update_db_fields(\App\Models\Translation::class, ['name', 'value'], 'tenant', 'resident');
        update_db_fields(\OwenIt\Auditing\Models\Audit::class, ['old_values', 'new_values', 'auditable_type'], 'tenant', 'resident');
        update_db_fields(\App\Models\Media::class, ['model_type', 'collection_name', 'disk'], 'tenant', 'resident');



        // move media files,
        // email template
        // translations
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
