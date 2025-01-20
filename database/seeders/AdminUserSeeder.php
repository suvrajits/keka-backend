<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'suvrajit@igamerjam.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('premlat@98'),
                'user_type' => 'admin',
            ]
        );
    }
}