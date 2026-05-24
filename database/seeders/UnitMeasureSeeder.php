<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitMeasureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $measures = [
            ['code' => 'NIU', 'name' => 'Unidad (Bienes)', 'country' => 'PE'],
            ['code' => 'ZZ',  'name' => 'Servicio', 'country' => 'PE'],
            ['code' => 'UN',  'name' => 'Unidad (Chile)', 'country' => 'CL'],
            ['code' => 'KG',  'name' => 'Kilogramos', 'country' => null],
            ['code' => 'BX',  'name' => 'Caja', 'country' => null],
            ['code' => 'MT',  'name' => 'Metros', 'country' => null],
        ];

        foreach ($measures as $measure) {
            DB::table('unit_measures')->updateOrInsert(
                ['code' => $measure['code']],
                [
                    'name' => $measure['name'], 
                    'country' => $measure['country'], 
                    'updated_at' => now(), 
                    'created_at' => now()
                ]
            );
        }
    }
}
