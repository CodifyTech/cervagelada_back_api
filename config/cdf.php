<?php

use App\Domains\Auth\Models\User;

return [
    /*
    |--------------------------------------------------------------------------
    | Default user role
    |--------------------------------------------------------------------------
    |
    | This value is the default user role id that will be assigned to new users
    | when they register.
    |
    | admin = Admin role, user = User role, customer = Customer Role - Check RoleSeeder for more
    |
    */

    'default_user_role_slug' => env('CDF_DEFAULT_ROLE_SLUG', 'consumidor'),

    /*
    |--------------------------------------------------------------------------
    | Delete old access tokens when logged in
    |--------------------------------------------------------------------------
    |
    | This value determines whether or not to delete old access tokens when
    | the users are logged in.
    |
    */

    'delete_previous_access_tokens_on_login' => env('CDF_DELETE_PREVIOUS_ACCESS_TOKENS_ON_LOGIN', false),

    'api_version' => env('CDF_API_VERSION', 'v1'),

    'locales' => [
        'pt_BR',
        'en',
        'es',
    ],

    'tenantTable' => 'lojas',
    'tenantColumn' => 'loja_id',
    'tenantModels' => [
        App\Domains\Loja\Models\Loja::class => [
            'list' => true,
            'read' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ],
        App\Domains\Auth\Models\User::class => [
            'list' => true,
            'read' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ],
        App\Domains\Pedido\Models\Pedido::class => [
            'list' => true,
            'read' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ],
        App\Domains\Avaliacao\Models\Avaliacao::class => [
            'list' => true,
            'read' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ],
        App\Domains\Endereco\Models\Endereco::class => [
            'list' => true,
            'read' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ],
        App\Domains\TransacoesFinanceiras\Models\TransacoesFinanceiras::class => [
            'list' => true,
            'read' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ],
        App\Domains\Loja\Models\HorarioLoja::class => [
            'list' => true,
            'read' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ],
        App\Domains\Pagamento\Models\Pagamento::class => [
            'list' => true,
            'read' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ],
        App\Domains\Configuracao\Models\Configuracao::class => [
            'list' => true,
            'read' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ],
        \App\Domains\Produto\Models\LojaProduto::class => [
            'list' => true,
            'read' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ],
    ],
];
