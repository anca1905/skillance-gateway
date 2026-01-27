<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::where('email', 'admin@skillance.id')->exists()) {
            return;
        }

        User::create([
            'name' => 'Admin',
            'email' => 'admin@skillance.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
