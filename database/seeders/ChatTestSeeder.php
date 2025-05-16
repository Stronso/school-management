<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;

class ChatTestSeeder extends Seeder
{
    public function run()
    {
        // Create a test admin user if not exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Test Admin',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        // Create a test conversation
        $conversation = Conversation::create([
            'name' => 'Test Conversation',
            'type' => 'private',
        ]);

        // Add admin to the conversation
        $conversation->users()->attach($admin->id);

        // Create a test message
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $admin->id,
            'content' => 'This is a test message from the admin.',
            'read_at' => null,
        ]);
    }
} 