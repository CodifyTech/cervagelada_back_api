<?php

namespace App\Domains\Pedido\Events;

use App\Domains\Pedido\Models\Pedido;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderReceived
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Pedido $pedido,
    ) {}
}
