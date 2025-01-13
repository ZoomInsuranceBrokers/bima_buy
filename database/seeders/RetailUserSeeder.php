<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RetailUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RetailUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $data = [
            [
                'name' => 'Deepak Bansal',
                'first_name' => 'Deepak',
                'last_name' => 'Bansal',
                'gender' => 'male',
                'email' => 'deepak.bansal@zoominsurancebrokers.com',
                'mobile' => '9350289805',
                'image_path' => 'profile_photos/default_photos/male.jpg',
            ],
           
        ];

        
        foreach ($data as $entry) {
            $retailUser = RetailUser::create(['name' => $entry['name']]);

            $user = User::create([
                'first_name' => $entry['first_name'],
                'last_name' => $entry['last_name'],
                'gender' => $entry['gender'],
                'email' => $entry['email'],
                'mobile' => $entry['mobile'],
                'image_path' => $entry['image_path'],
                'role_id' => 4,
                'password' => Hash::make('Novel@123'), 
            ]);
            
            $retailUser->update([
                'user_id' => $user->id,
            ]);
        }
        
    }
}
