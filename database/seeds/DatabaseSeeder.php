<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(TemplateCategoriesTableSeeder::class);
        $this->call(TemplateTableSeeder::class);
        $this->call(TranslationTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(StatesTableSeeder::class);
        $this->call(RequestCategoriesTableSeeder::class);

        $this->call(PermissionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);

        //$this->call(AddressesTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(QuartersTableSeeder::class);
        $this->call(BuildingsTableSeeder::class);
        $this->call(UnitsTableSeeder::class);

        $this->call(ServiceProvidersTableSeeder::class);
        $this->call(PropertyManagerTableSeeder::class);
        $this->call(ResidentsTableSeeder::class);

        $this->call(PinboardTableSeeder::class);
        $this->call(CommentsTableSeeder::class);
        $this->call(ListingsTableSeeder::class);

        $this->call(RequestsTableSeeder::class);

        $this->call(AuditsTableSeeder::class);
    }
}
