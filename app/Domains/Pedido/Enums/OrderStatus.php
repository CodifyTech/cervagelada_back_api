<?php

namespace App\Domains\Pedido\Enums;

enum OrderStatus: string
{
    case AGUARDANDO_PAGAMENTO = 'aguardando_pagamento';
    case RECEBIDO = 'recebido';
    case ACEITO = 'aceito';
    case PREPARANDO = 'preparando';
    case PRONTO = 'pronto';
    case EM_ROTA = 'em_rota';
    case ENTREGUE = 'entregue';
    case CANCELADO = 'cancelado';

    /**
     * Valid transitions from the current status.
     *
     * @return self[]
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::AGUARDANDO_PAGAMENTO => [self::RECEBIDO, self::CANCELADO],
            self::RECEBIDO => [self::ACEITO, self::CANCELADO],
            self::ACEITO => [self::PREPARANDO, self::CANCELADO],
            self::PREPARANDO => [self::PRONTO, self::CANCELADO],
            self::PRONTO => [self::EM_ROTA, self::CANCELADO],
            self::EM_ROTA => [self::ENTREGUE],
            self::ENTREGUE => [],
            self::CANCELADO => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions());
    }

    public function label(): string
    {
        return match ($this) {
            self::AGUARDANDO_PAGAMENTO => 'Aguardando Pagamento',
            self::RECEBIDO => 'Recebido',
            self::ACEITO => 'Aceito',
            self::PREPARANDO => 'Preparando',
            self::PRONTO => 'Pronto',
            self::EM_ROTA => 'Em Rota',
            self::ENTREGUE => 'Entregue',
            self::CANCELADO => 'Cancelado',
        };
    }

    /**
     * Statuses visible to the seller (after payment confirmed).
     *
     * @return self[]
     */
    public static function sellerVisible(): array
    {
        return [
            self::RECEBIDO,
            self::ACEITO,
            self::PREPARANDO,
            self::PRONTO,
            self::EM_ROTA,
            self::ENTREGUE,
            self::CANCELADO,
        ];
    }

    /**
     * Whether seller can manually trigger this transition.
     */
    public function isSellerActionable(): bool
    {
        return in_array($this, [
            self::RECEBIDO,
            self::ACEITO,
            self::PREPARANDO,
            self::PRONTO,
        ]);
    }

    /**
     * All values as string array.
     *
     * @return string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Values as comma-separated string for validation rules.
     */
    public static function valuesString(): string
    {
        return implode(',', self::values());
    }
}
