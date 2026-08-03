<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FishStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fishTypes = DB::table('fish_types')->orderBy('id')->get();

        // Alert thresholds are real config values — owner will manage actual stock via dashboard
        $thresholds = [
            'Patin'  => 50,
            'Nila'   => 25,
            'Gurame' => 10,
            'Bawal'  => 5,
            'Lele'   => 20,
        ];

        $stocks = [
            'Patin'  => 85,
            'Nila'   => 120,
            'Gurame' => 45,
            'Bawal'  => 60,
            'Lele'   => 95,
        ];

        foreach ($fishTypes as $fishType) {
            DB::table('fish_stocks')->updateOrInsert(
                ['fish_type_id' => $fishType->id],
                [
                    'current_stock_kg'   => $stocks[$fishType->name] ?? 50,
                    'alert_threshold_kg' => $thresholds[$fishType->name] ?? 20,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]
            );
        }
    }
}