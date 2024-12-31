<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Notification;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch all users
        $users = User::all();

        if ($users->count() < 2) {
            $this->command->info('At least 2 users are required to seed notifications.');
            return;
        }

        // Generate random notifications
        foreach ($users as $receiver) {
            // Randomly pick a sender different from the receiver
            $sender = $users->where('id', '!=', $receiver->id)->random();

            // Create notifications
            Notification::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'message' => 'This is a test message from ' . $sender->name,
                'is_read' => rand(0, 1), // Randomly set as read or unread
                'created_at' => now()->subMinutes(rand(1, 1440)), // Random time within the last 24 hours
            ]);
        }

        $this->command->info('Notifications table seeded successfully!');
    }

}

