<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EnterpriseSimulationSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            HealthRecordSeeder::class,
            DonationSeeder::class,
        ]);
    }
}
