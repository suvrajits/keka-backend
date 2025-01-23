<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        AdminUser::updateOrCreate(
            ['email' => 'suvrajit@igamerjam.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('premlat@98'),
            ]
        );
    }
}
