<?php

namespace Database\Seeders\Crm\Funnels;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FunnelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $funnels = [
            'Status do Lead' => [
                'role' => 2, // Funis de contatos
                'stages' => [
                    [
                        'name'                 => 'Novo',
                        'business_probability' => null,
                        'order'                => 1,
                    ],
                    [
                        'name'                 => 'Abrir',
                        'business_probability' => null,
                        'order'                => 2,
                    ],
                    [
                        'name'                 => 'Em andamento',
                        'business_probability' => null,
                        'order'                => 3,
                    ],
                    [
                        'name'                 => 'Negócio aberto',
                        'business_probability' => null,
                        'order'                => 4,
                    ],
                    [
                        'name'                 => 'Não qualificado',
                        'business_probability' => null,
                        'order'                => 5,
                    ],
                    [
                        'name'                 => 'Tentou entrar em contato',
                        'business_probability' => null,
                        'order'                => 6,
                    ],
                    [
                        'name'                 => 'Conectado',
                        'business_probability' => null,
                        'order'                => 7,
                    ],
                    [
                        'name'                 => 'Momento ruim',
                        'business_probability' => null,
                        'order'                => 8,
                    ]
                ]
            ],
            'Funil de Vendas' => [
                'role' => 1, // Funis de negócios
                'stages' => [
                    [
                        'name'                 => 'Compromisso agendado',
                        'business_probability' => 20,
                        'order'                => 1,
                    ],
                    [
                        'name'                 => 'Qualificado para comprar',
                        'business_probability' => 40,
                        'order'                => 2,
                    ],
                    [
                        'name'                 => 'Apresentação agendada',
                        'business_probability' => 60,
                        'order'                => 3,
                    ],
                    [
                        'name'                 => 'Tomador de decisão envolvido',
                        'business_probability' => 80,
                        'order'                => 4,
                    ],
                    [
                        'name'                 => 'Contrato enviado',
                        'business_probability' => 90,
                        'order'                => 5,
                    ],
                    [
                        'name'                 => 'Negócio fechado',
                        'business_probability' => 100,
                        'order'                => 6,
                    ],
                    [
                        'name'                 => 'Negócio perdido',
                        'business_probability' => 0,
                        'order'                => 7,
                    ]
                ]
            ],
            'Funil da Loja' => [
                'role' => 1, // Funis de negócios
                'stages' => [
                    [
                        'name'                 => 'Interesse',
                        'business_probability' => 10,
                        'order'                => 1,
                    ],
                    [
                        'name'                 => 'Carrinho',
                        'business_probability' => 30,
                        'order'                => 2,
                    ],
                    [
                        'name'                 => 'Checkout',
                        'business_probability' => 60,
                        'order'                => 3,
                    ],
                    [
                        'name'                 => 'Venda realizada',
                        'business_probability' => 100,
                        'order'                => 4,
                    ],
                    [
                        'name'                 => 'Venda perdida',
                        'business_probability' => 0,
                        'order'                => 5,
                    ]
                ]
            ],
        ];

        foreach ($funnels as $funnelName => $data) {
            $funnelId = DB::table('crm_funnels')->insertGetId([
                'role'       => $data['role'],
                'name'       => $funnelName,
                // 'slug'       => Str::slug($funnelName),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            foreach ($data['stages'] as $stage) {
                DB::table('crm_funnel_stages')->insert([
                    'funnel_id'            => $funnelId,
                    'name'                 => $stage['name'],
                    // 'slug'                 => Str::slug($stage['name']),
                    'business_probability' => $stage['business_probability'],
                    'created_at'           => now(),
                    'updated_at'           => now()
                ]);
            }
        }
    }
}
