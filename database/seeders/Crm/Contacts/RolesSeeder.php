<?php

namespace Database\Seeders\Crm\Contacts;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Assinante',
            'Lead',
            'Cliente',
            'Promotor',
            'Fornecedor',
            'Outro'
        ];

        foreach ($roles as $role) {
            DB::table('crm_contact_roles')->insert([
                'name'       => $role,
                'slug'       => Str::slug($role),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
