<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'username' => 'smartbudget',
            'email'    => 'user@smartbudget.test',
            'password' => Hash::make('password'),
        ]);

        Profile::create([
            'user_id'  => $user->id,
            'currency' => 'EUR',
            'language' => 'es',
            'timezone' => 'Europe/Madrid',
        ]);
    }
}