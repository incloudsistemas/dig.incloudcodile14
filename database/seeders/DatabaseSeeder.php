<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Crm\Contacts\RolesSeeder as ContactRolesSeeder;
use Database\Seeders\Crm\Contacts\SourcesSeeder;
use Database\Seeders\Crm\Funnels\FunnelsSeeder;
use Database\Seeders\Shop\ProductCategoriesForWomenStoresSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            // UsersSeeder::class,

            // ProductCategoriesForWomenStoresSeeder::class,

            FunnelsSeeder::class,
            SourcesSeeder::class,
            ContactRolesSeeder::class,
        ]);

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
