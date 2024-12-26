<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ZonalManager;

class ZonalManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ZonalManager::create(['name' => 'Ramu']);
        ZonalManager::create(['name' => 'Praveen']);
        ZonalManager::create(['name' => 'Naveen']);
        ZonalManager::create(['name' => 'Suresh']);
        
    }
}
