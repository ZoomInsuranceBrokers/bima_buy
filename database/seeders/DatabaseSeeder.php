<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\ZonalManager;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RetailUserSeeder;
use Database\Seeders\ZonalManagerSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            RetailUserSeeder::class,
            ZonalManagerSeeder::class,
        ]);
    }
}
