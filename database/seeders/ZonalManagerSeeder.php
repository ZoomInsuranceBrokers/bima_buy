<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ZonalManager;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ZonalManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $data = [
            [
                'name' => 'Vanshika',
                'first_name' => 'Vanshika',
                'last_name' => 'Naidu',
                'gender' => 'female',
                'email' => 'vanshika@novelhealthtech.com',
                'mobile' => '8929292718',
                'image_path' => 'profile_photos/default_photos/female.jpg',
            ],
            [
                'name' => 'Deepanshu Manchanda',
                'first_name' => 'Deepanshu',
                'last_name' => 'Manchanda',
                'gender' => 'male',
                'email' => 'deepanshu.manchanda@novelhealthtech.com',
                'mobile' => '9837461839',
                'image_path' => 'profile_photos/default_photos/male.jpg',
            ],
            [
                'name' => 'Sandeep Yadav',
                'first_name' => 'Sandeep',
                'last_name' => 'Yadav',
                'gender' => 'male',
                'email' => 'sandeep.yadav@novelhealthtech.com',
                'mobile' => '9876543211',
                'image_path' => 'profile_photos/default_photos/male.jpg',
            ],
            [
                'name' => 'Virender Singh Negi',
                'first_name' => 'Virender',
                'last_name' => 'Singh Negi',
                'gender' => 'male',
                'email' => 'virender.negi@novelhealthtech.com',
                'mobile' => '9876543214',
                'image_path' => 'profile_photos/default_photos/male.jpg',
            ],
            [
                'name' => 'Saksham Singh',
                'first_name' => 'Saksham',
                'last_name' => 'Singh',
                'gender' => 'male',
                'email' => 'saksham.singh@novelhealthtech.com',
                'mobile' => '9876543212',
                'image_path' => 'profile_photos/default_photos/male.jpg',
            ],
            [
                'name' => 'P Santhosh',
                'first_name' => 'P',
                'last_name' => 'Santhosh',
                'gender' => 'female',
                'email' => 'santhosh@novelhealthtech.com',
                'mobile' => '9876543213',
                'image_path' => 'profile_photos/default_photos/female.jpg',
            ],
           
        ];

        
        foreach ($data as $entry) {
            $zonalManager = ZonalManager::create(['name' => $entry['name']]);

            $user = User::create([
                'first_name' => $entry['first_name'],
                'last_name' => $entry['last_name'],
                'gender' => $entry['gender'],
                'email' => $entry['email'],
                'mobile' => $entry['mobile'],
                'image_path' => $entry['image_path'],
                'role_id' => 3,
                'zm_id' => $zonalManager->id,
                'password' => Hash::make('Novel@123'), 
            ]);
            
            $zonalManager->update([
                'user_id' => $user->id,
            ]);
        }
        
    }
}
