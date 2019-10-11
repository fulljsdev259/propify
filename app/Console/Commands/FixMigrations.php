<?php

namespace App\Console\Commands;

use App\Models\Building;
use App\Models\PropertyManager;
use App\Models\Quarter;
use App\Models\ServiceProvider;
use App\Models\Request;
use App\Models\Resident;
use App\Models\Unit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix-migrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix migrations table values';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $latest = DB::table('migrations')->latest('id')->first();

        if (
            $latest
            && 92 == $latest->id
            && '2019_10_07_130953_add_foreign_keys_to_user_settings_table' == $latest->migration
            && 1 == $latest->batch
        ) {
            echo 'migrations fixed';
            return;
        }

        DB::table('migrations')->truncate();
        DB::statement("INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
            (1, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
            (2, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
            (3, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
            (4, '2016_06_01_000004_create_oauth_clients_table', 1),
            (5, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
            (6, '2016_09_02_153301_create_love_likes_table', 1),
            (7, '2016_09_02_163301_create_love_like_counters_table', 1),
            (8, '2019_10_07_130952_create_announcement_email_receptionists_table', 1),
            (9, '2019_10_07_130952_create_audits_table', 1),
            (10, '2019_10_07_130952_create_autologins_table', 1),
            (11, '2019_10_07_130952_create_building_assignees_table', 1),
            (12, '2019_10_07_130952_create_building_pinboard_table', 1),
            (13, '2019_10_07_130952_create_building_service_provider_table', 1),
            (14, '2019_10_07_130952_create_buildings_table', 1),
            (15, '2019_10_07_130952_create_cleanify_requests_table', 1),
            (16, '2019_10_07_130952_create_comments_table', 1),
            (17, '2019_10_07_130952_create_conversation_user_table', 1),
            (18, '2019_10_07_130952_create_conversations_table', 1),
            (19, '2019_10_07_130952_create_internal_notices_table', 1),
            (20, '2019_10_07_130952_create_jobs_table', 1),
            (21, '2019_10_07_130952_create_listings_table', 1),
            (22, '2019_10_07_130952_create_loc_addresses_table', 1),
            (23, '2019_10_07_130952_create_loc_countries_table', 1),
            (24, '2019_10_07_130952_create_loc_states_table', 1),
            (25, '2019_10_07_130952_create_login_devices_table', 1),
            (26, '2019_10_07_130952_create_media_table', 1),
            (27, '2019_10_07_130952_create_notifications_table', 1),
            (28, '2019_10_07_130952_create_password_resets_table', 1),
            (29, '2019_10_07_130952_create_permission_role_table', 1),
            (30, '2019_10_07_130952_create_permissions_table', 1),
            (31, '2019_10_07_130952_create_pinboard_quarter_table', 1),
            (32, '2019_10_07_130952_create_pinboard_service_provider_table', 1),
            (33, '2019_10_07_130952_create_pinboard_table', 1),
            (34, '2019_10_07_130952_create_pinboard_view_table', 1),
            (35, '2019_10_07_130952_create_property_managers_table', 1),
            (36, '2019_10_07_130952_create_quarter_assignees_table', 1),
            (37, '2019_10_07_130952_create_quarter_service_provider_table', 1),
            (38, '2019_10_07_130952_create_quarters_table', 1),
            (39, '2019_10_07_130952_create_request_assignees_table', 1),
            (40, '2019_10_07_130952_create_request_categories_table', 1),
            (41, '2019_10_07_130952_create_request_tag_table', 1),
            (42, '2019_10_07_130952_create_requests_table', 1),
            (43, '2019_10_07_130952_create_role_user_table', 1),
            (44, '2019_10_07_130952_create_roles_table', 1),
            (45, '2019_10_07_130952_create_service_providers_table', 1),
            (46, '2019_10_07_130952_create_settings_table', 1),
            (47, '2019_10_07_130952_create_tags_table', 1),
            (48, '2019_10_07_130952_create_template_categories_table', 1),
            (49, '2019_10_07_130952_create_templates_table', 1),
            (50, '2019_10_07_130952_create_contracts_table', 1),
            (51, '2019_10_07_130952_create_resident_unit_table', 1),
            (52, '2019_10_07_130952_create_residents_table', 1),
            (53, '2019_10_07_130952_create_translations_table', 1),
            (54, '2019_10_07_130952_create_units_table', 1),
            (55, '2019_10_07_130952_create_user_settings_table', 1),
            (56, '2019_10_07_130952_create_users_table', 1),
            (57, '2019_10_07_130953_add_foreign_keys_to_announcement_email_receptionists_table', 1),
            (58, '2019_10_07_130953_add_foreign_keys_to_autologins_table', 1),
            (59, '2019_10_07_130953_add_foreign_keys_to_building_assignees_table', 1),
            (60, '2019_10_07_130953_add_foreign_keys_to_building_pinboard_table', 1),
            (61, '2019_10_07_130953_add_foreign_keys_to_building_service_provider_table', 1),
            (62, '2019_10_07_130953_add_foreign_keys_to_buildings_table', 1),
            (63, '2019_10_07_130953_add_foreign_keys_to_cleanify_requests_table', 1),
            (64, '2019_10_07_130953_add_foreign_keys_to_comments_table', 1),
            (65, '2019_10_07_130953_add_foreign_keys_to_conversation_user_table', 1),
            (66, '2019_10_07_130953_add_foreign_keys_to_listings_table', 1),
            (67, '2019_10_07_130953_add_foreign_keys_to_loc_addresses_table', 1),
            (68, '2019_10_07_130953_add_foreign_keys_to_loc_states_table', 1),
            (69, '2019_10_07_130953_add_foreign_keys_to_login_devices_table', 1),
            (70, '2019_10_07_130953_add_foreign_keys_to_permission_role_table', 1),
            (71, '2019_10_07_130953_add_foreign_keys_to_pinboard_quarter_table', 1),
            (72, '2019_10_07_130953_add_foreign_keys_to_pinboard_service_provider_table', 1),
            (73, '2019_10_07_130953_add_foreign_keys_to_pinboard_table', 1),
            (74, '2019_10_07_130953_add_foreign_keys_to_pinboard_view_table', 1),
            (75, '2019_10_07_130953_add_foreign_keys_to_property_managers_table', 1),
            (76, '2019_10_07_130953_add_foreign_keys_to_quarter_assignees_table', 1),
            (77, '2019_10_07_130953_add_foreign_keys_to_quarter_service_provider_table', 1),
            (78, '2019_10_07_130953_add_foreign_keys_to_quarters_table', 1),
            (79, '2019_10_07_130953_add_foreign_keys_to_request_assignees_table', 1),
            (80, '2019_10_07_130953_add_foreign_keys_to_request_categories_table', 1),
            (81, '2019_10_07_130953_add_foreign_keys_to_request_tag_table', 1),
            (82, '2019_10_07_130953_add_foreign_keys_to_requests_table', 1),
            (83, '2019_10_07_130953_add_foreign_keys_to_role_user_table', 1),
            (84, '2019_10_07_130953_add_foreign_keys_to_service_providers_table', 1),
            (85, '2019_10_07_130953_add_foreign_keys_to_settings_table', 1),
            (86, '2019_10_07_130953_add_foreign_keys_to_template_categories_table', 1),
            (87, '2019_10_07_130953_add_foreign_keys_to_templates_table', 1),
            (88, '2019_10_07_130953_add_foreign_keys_to_resident_contracts_table', 1),
            (89, '2019_10_07_130953_add_foreign_keys_to_resident_unit_table', 1),
            (90, '2019_10_07_130953_add_foreign_keys_to_residents_table', 1),
            (91, '2019_10_07_130953_add_foreign_keys_to_units_table', 1),
            (92, '2019_10_07_130953_add_foreign_keys_to_user_settings_table', 1);
        ");

        echo 'migrations fixed';
        return;
    }
}
