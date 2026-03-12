<?php

namespace App\Domains\Pedido\Listeners;

use App\Domains\Auditoria\Services\AuditService;
use App\Domains\Pedido\Events\NewOrderReceived;
use App\Domains\Pedido\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Log;

class SendNewOrderNotification
{
    public function __construct(
        private readonly AuditService $auditService = new AuditService(),
    ) {}

    public function handle(NewOrderReceived $event): void
    {
        $pedido = $event->pedido;
        $loja = $pedido->loja;

        if (!$loja) {
            Log::warning("[SendNewOrderNotification] Loja not found for pedido {$pedido->id}");
            return;
        }

        // Find the store owner (user with loja_id matching)
        $owner = \App\Domains\Auth\Models\User::where('loja_id', $loja->id)->first();

        if (!$owner) {
            Log::warning("[SendNewOrderNotification] Owner not found for loja {$loja->id}");
            return;
        }

        try {
            $owner->notify(new NewOrderNotification($pedido));

            $this->auditService->log('notification_sent', 'pedido', $pedido->id, null, null, [
                'type' => 'new_order',
                'loja_id' => $loja->id,
                'recipient_id' => $owner->id,
            ]);
        } catch (\Throwable $e) {
            Log::error("[SendNewOrderNotification] Failed for pedido {$pedido->id}: {$e->getMessage()}");

            $this->auditService->log('notification_failed', 'pedido', $pedido->id, null, null, [
                'type' => 'new_order',
                'loja_id' => $loja->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
