<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seeder ini membuat akun-akun demo agar kamu bisa langsung
     * login dan testing tanpa harus daftar manual berkali-kali.
     */
    public function run(): void
    {
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@digisign.test',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'John Doe',
            'email'    => 'john@digisign.test',
            'password' => bcrypt('password123'),
            'role'     => 'user',
        ]);

        User::create([
            'name'     => 'Sarah Kusuma',
            'email'    => 'sarah@digisign.test',
            'password' => bcrypt('password123'),
            'role'     => 'user',
        ]);

        User::create([
            'name'     => 'Budi Santoso',
            'email'    => 'budi@digisign.test',
            'password' => bcrypt('password123'),
            'role'     => 'user',
        ]);
    }
}
