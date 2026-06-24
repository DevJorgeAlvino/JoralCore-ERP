<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            SuperAdminSeeder::class,
            CompanySeeder::class,
            SettingsSeeder::class,
            UnitMeasureSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            ItemSeeder::class,
            ItemPresentationSeeder::class,
        ]);

    }
}
