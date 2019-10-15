<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixTenantRelatedValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        update_db_fields(\OwenIt\Auditing\Models\Audit::class, ['new_values', 'old_values', 'auditable_type'], 'rent_contract', 'contract', 'false');
        update_db_fields(\OwenIt\Auditing\Models\Audit::class, ['new_values', 'old_values', 'auditable_type'], 'tenant', 'resident');
        update_db_fields(\App\Models\Permission::class, ['display_name', 'name'], 'tenant', 'resident');
        update_db_fields(\App\Models\Role::class, ['display_name'], 'tenant', 'resident');
        update_db_fields(\App\Models\Template::class, [ 'name', 'subject', 'body',], 'tenant', 'resident');
        update_db_fields(\App\Models\TemplateCategory::class, ['name', 'subject', 'body','tag_map', 'description'], 'tenant', 'resident');
        update_db_fields(\Illuminate\Notifications\DatabaseNotification::class, ['type', 'data'], 'tenant', 'resident');
        update_db_fields(\App\Models\Translation::class, ['value'], 'post', 'pinboard');
        update_db_fields(\App\Models\Translation::class, ['value'], 'tenant', 'resident');
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
