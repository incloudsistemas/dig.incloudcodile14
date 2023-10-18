<?php

namespace Database\Seeders\Crm\Contacts;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SourcesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            'Pesquisa Orgânica',
            'Pesquisa Paga',
            'Email Marketing',
            'Mídia Social',
            'Referências',
            'Outras Campanhas',
            'Tráfego Direto',
            'Fontes Offline'
        ];

        foreach ($sources as $source) {
            DB::table('crm_contact_sources')->insert([
                'name'       => $source,
                'slug'       => Str::slug($source),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
