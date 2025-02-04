<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdminUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $data = [
            [
                'name' => 'Naveen Patial',
                'first_name' => 'Naveen',
                'last_name' => 'Patial',
                'gender' => 'male',
                'email' => 'naveen.patial@novelhealthtech.com',
                'mobile' => '1234567890',
                'image_path' => 'profile_photos/default_photos/male.jpg',
            ],
           
        ];

        
        foreach ($data as $entry) {
            $adminUser = AdminUser::create(['name' => $entry['name']]);

            $user = User::create([
                'first_name' => $entry['first_name'],
                'last_name' => $entry['last_name'],
                'gender' => $entry['gender'],
                'email' => $entry['email'],
                'mobile' => $entry['mobile'],
                'image_path' => $entry['image_path'],
                'role_id' => 1,
                'password' => Hash::make('Novel@123'), 
            ]);
            
            $adminUser->update([
                'user_id' => $user->id,
            ]);
        }
        
    }
}
