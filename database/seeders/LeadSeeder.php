<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Get the list of user IDs and zonal manager IDs
        $userIds = DB::table('users')->pluck('id');
        $zmIds = DB::table('zonal_managers')->pluck('id');

        // Create 10 sample leads
        foreach (range(1, 10) as $index) {
            DB::table('leads')->insert([
                'user_id' => $faker->randomElement($userIds),
                'zm_id' => $faker->randomElement($zmIds),
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'gender' => $faker->randomElement(['male', 'female']),
                'date_of_birth' => $faker->date(),
                'mobile_no' => $faker->phoneNumber,
                'vehicle_number' => strtoupper($faker->bothify('??-###-####')),
                'is_issue' => $faker->boolean(),
                'is_zm_verified' => $faker->boolean(),
                'is_retail_verified' => $faker->boolean(),
                'is_cancel' => $faker->boolean(),
                'is_payment_complete' => $faker->boolean(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
