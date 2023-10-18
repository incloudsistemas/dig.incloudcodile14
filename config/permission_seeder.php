<?php

return [
    /**
     * Control if the seeder should create a user per role while seeding the data.
     */
    'create_users' => false,

    /**
     * Control if all the permissions tables should be truncated before running the seeder.
     */
    'truncate_tables' => true,

    'roles_structure' => [
        'Superadministrador' => [
            // 'Permissões' => 'c,r,u,d',
        ],
        'Cliente' => [
            //
        ],
        'Administrador' => [
            'Níveis de Acessos' => 'c,r,u,d',
            'Usuários'          => 'c,r,u,d',

            '[Cms] Páginas'                  => 'c,r,u,d',
            '[Cms] Blog'                     => 'c,r,u,d',
            '[Cms] Produtos'                 => 'c,r,u,d',
            '[Cms] Serviços'                 => 'c,r,u,d',
            '[Cms] Portfólio'                => 'c,r,u,d',
            '[Cms] Depoimentos'              => 'c,r,u,d',
            '[Cms] Parceiros'                => 'c,r,u,d',
            '[Cms] Membros da Equipe'        => 'c,r,u,d',
            '[Cms] Links Externos'           => 'c,r,u,d',
            // '[Cms] Calendário de Eventos' => 'c,r,u,d',
            // '[Cms] Iscas Digitais'        => 'c,r,u,d',

            '[Cms] Sliders'                  => 'c,r,u,d',
            '[Cms] Categorias'               => 'c,r,u,d',

            '[Shop] Produtos'         => 'c,r,u,d',
            '[Shop] Categorias'       => 'c,r,u,d',
            '[Shop] Marcas'           => 'c,r,u,d',
            '[Shop] Estoque'          => 'c,r,u,d',
            '[Shop] Vendas / Pedidos' => 'c,r,u,d', // Business

            '[CRM] Funis de Negócios'     => 'c,r,u,d',
            '[CRM] Funis de Contatos'     => 'c,r,u,d',
            '[CRM] Origens dos Contatos'  => 'c,r,u,d',
            '[CRM] Tipos de Contatos'     => 'c,r,u,d',
            '[CRM] Contatos P. Físicas'   => 'c,r,u,d',
            '[CRM] Contatos P. Jurídicas' => 'c,r,u,d',
        ],
    ],

    'permissions_map' => [
        'c' => 'Cadastrar',
        'r' => 'Visualizar',
        'u' => 'Editar',
        'd' => 'Deletar'
    ]
];
