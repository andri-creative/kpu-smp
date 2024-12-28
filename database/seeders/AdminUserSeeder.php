<?php

namespace Database\Seeders;

use App\Models\AdminUserModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AdminUserModel::create([
            'name_admin' => 'Admin Name',
            'username_admin' => '000000',
            'password_admin' => bcrypt('password123'),
            'role_admin' => 'admin',
        ]);
    }
}