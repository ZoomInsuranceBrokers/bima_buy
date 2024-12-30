<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Loop to create 20 users
        foreach (range(1, 20) as $index) {
            DB::table('users')->insert([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'gender' => $faker->randomElement(['male', 'female']),
                'email' => $faker->unique()->safeEmail,
                'mobile' => $faker->regexify('[0-9]{10}'),
                'image_path' => $faker->randomElement(['male', 'female']) == 'male'
                    ? 'profile_photos/default_photos/male.jpg'
                    : 'profile_photos/default_photos/female.jpg',
                'role_id' => rand(1, 5),
                'zm_id' => rand(1, 4),
                'email_verified_at' => now(),
                'password' => Hash::make('Novel@123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
