<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // user superadmin
        $superadmin = User::create([
            'name'              => 'Vinícius C. Lemos',
            'email'             => 'contato@incloudsistemas.com.br',
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
            'remember_token'    => Str::random(10),
        ]);

        $superadmin->assignRole('Superadministrador');
    }
}
