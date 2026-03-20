<?php

namespace App\Domains\ACL\Enums;

enum RoleEnum: string
{
    case Admin = 'admin';
    case AdminSystem = 'admin-system';
    case Logista = 'logista';
    case Funcionario = 'funcionario';
    case Entregador = 'entregador';
    case Consumidor = 'consumidor';

    public function getPermissions(): array
    {
        return match ($this) {
            self::Admin => [
                ...config('permission_list.auth'),
                ...config('permission_list.manage'),
            ],
            self::AdminSystem => [
                ...config('permission_list.auth'),
                ...config('permission_list.profile'),
                ...config('permission_list.user'),
                ...config('permission_list.roles'),
                ...config('permission_list.permission'),
                ...config('permission_list.configuracao'),
                ...config('permission_list.audit'),
                ...config('permission_list.relatorios'),
                ...config('permission_list.dashboard'),
                ...config('permission_list.produto-aprovacao'),
                ...config('permission_list.loja'),
                ...config('permission_list.produto'),
                ...config('permission_list.pedido'),
                ...config('permission_list.item-pedido'),
                ...config('permission_list.avaliacao'),
                ...config('permission_list.transacoes-financeiras'),
                ...config('permission_list.destaque'),
                ...config('permission_list.endereco'),
                ...config('permission_list.entrega'),
            ],
            self::Logista => [
                ...config('permission_list.auth'),
                ...config('permission_list.profile'),
                ...config('permission_list.dashboard'),
                ...config('permission_list.relatorios'),
                'loja read',
                'loja edit',
                'produto list',
                'produto create',
                'produto read',
                // ...config('permission_list.produto'),
                'pedido read',
                'pedido list',
                'pedido edit',
                'item-pedido read',
                'item-pedido list',
                ...config('permission_list.promocao'),
                // 'produto-aprovacao read',
                // 'produto-aprovacao list',
                'avaliacao read',
                'avaliacao list',
                'transacoes-financeiras read',
                'transacoes-financeiras list',
                ...config('permission_list.destaque'),
                'endereco read',
                'endereco edit',
                ...config('permission_list.entrega'),
            ],
            self::Funcionario => [
                ...config('permission_list.auth'),
                ...config('permission_list.profile'),
                ...config('permission_list.dashboard'),
                'produto read',
                'produto list',
                'produto edit',
                'pedido read',
                'pedido list',
                'pedido edit',
                'item-pedido read',
                'item-pedido list',
                'avaliacao read',
                'avaliacao list',
                'promocao read',
                'promocao list',
                'destaque read',
                'destaque list',
                'entrega read',
                'entrega list',
            ],
            self::Entregador => [
                ...config('permission_list.auth'),
                ...config('permission_list.profile'),
                ...config('permission_list.dashboard'),
                'pedido read',
                'pedido list',
                'pedido edit',
                'item-pedido read',
                'item-pedido list',
                'loja read',
                'endereco read',
                'entrega read',
                'entrega list',
                'entrega edit',
                'avaliacao read',
                'avaliacao list',
            ],
            self::Consumidor => [
                ...config('permission_list.auth'),
                ...config('permission_list.profile'),
                ...config('permission_list.dashboard'),
                'loja read',
                'loja list',
                'produto read',
                'produto list',
                'pedido create',
                'pedido read',
                'pedido list',
                'item-pedido create',
                'item-pedido read',
                'item-pedido list',
                'avaliacao create',
                'avaliacao read',
                'avaliacao list',
                'avaliacao edit',
                'avaliacao delete',
                'endereco create',
                'endereco read',
                'endereco list',
                'endereco edit',
                'endereco delete',
                'promocao read',
                'promocao list',
                'destaque read',
                'destaque list',
                'transacoes-financeiras read',
                'transacoes-financeiras list',
                'entrega read',
            ],
        };
    }

    public function getRoleName(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::AdminSystem => 'Admin System',
            self::Logista => 'Logista',
            self::Funcionario => 'Funcionário',
            self::Entregador => 'Entregador',
            self::Consumidor => 'Consumidor',
        };
    }
}
