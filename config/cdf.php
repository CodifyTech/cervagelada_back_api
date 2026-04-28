<?php

use App\Domains\Auth\Models\User;
use App\Domains\Avaliacao\Models\Avaliacao;
use App\Domains\Endereco\Models\Endereco;
use App\Domains\Loja\Models\HorarioLoja;
use App\Domains\Loja\Models\Loja;
use App\Domains\Pagamento\Models\Pagamento;
use App\Domains\Pedido\Models\Pedido;
use App\Domains\Produto\Models\LojaProduto;
use App\Domains\TransacoesFinanceiras\Models\TransacoesFinanceiras;
use App\Domains\Destaque\Models\Destaque;

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

    'rollback_log_path' => storage_path('framework/rollback/rollback_log.json'),

    'dir_front_end' => env('CDF_DIR_FRONT_END', '..'.DIRECTORY_SEPARATOR.'frontend'.DIRECTORY_SEPARATOR.'src'),

    'front_end_url' => env('CDF_FRONT_END_URL', 'localhost:5173'),

    'locales' => [
        'pt_BR',
        'en',
        'es',
    ],

    'tenantTable' => 'lojas',
    'tenantColumn' => 'loja_id',
    'tenantModels' => [
        Loja::class => [
            'list' => true,
            'read' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ],
        User::class => [
            'list' => true,
            'read' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ],
        Pedido::class => [
            'list' => true,
            'read' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ],
        Avaliacao::class => [
            'list' => true,
            'read' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ],
        Endereco::class => [
            'list' => true,
            'read' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ],
        TransacoesFinanceiras::class => [
            'list' => true,
            'read' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ],
        HorarioLoja::class => [
            'list' => true,
            'read' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ],
        Pagamento::class => [
            'list' => true,
            'read' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ],
        LojaProduto::class => [
            'list' => true,
            'read' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ],
        Destaque::class => [
            'list' => true,
            'read' => true,
            'create' => true,
            'update' => true,
            'delete' => true,
        ],
    ],
];
