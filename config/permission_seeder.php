<?php

return [
    /**
     * Control if the seeder should create a user per role while seeding the data.
     */
    'create_users' => true,

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
            'Usuários' => 'c,r,u,d',           
            '[Cms] Páginas' => 'c,r,u,d',
            '[Cms] Blog' => 'c,r,u,d',
            // '[Cms] Serviços' => 'c,r,u,d',
            // '[Cms] Produtos' => 'c,r,u,d',            
            // '[Cms] Materiais Ricos' => 'c,r,u,d',
            // '[Cms] Cursos' => 'c,r,u,d',
            // '[Cms] Calendário de Eventos' => 'c,r,u,d',
            // '[Cms] Links Externos' => 'c,r,u,d',
            // '[Cms] Portfólio' => 'c,r,u,d',
            // '[Cms] Depoimentos' => 'c,r,u,d',
            // '[Cms] Parceiros' => 'c,r,u,d',
            // '[Cms] Membros da Equipe' => 'c,r,u,d',
            '[Cms] Categorias' => 'c,r,u,d',
        ],
    ],

    'permissions_map' => [
        'c' => 'Cadastrar',
        'r' => 'Visualizar',
        'u' => 'Editar',
        'd' => 'Deletar'
    ]
];
