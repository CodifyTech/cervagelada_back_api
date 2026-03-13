<?php

namespace App\Domains\Pedido\Commands;

use App\Domains\Auditoria\Services\AuditService;
use App\Domains\Pedido\Enums\OrderStatus;
use App\Domains\Pedido\Models\Pedido;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpireUnpaidOrders extends Command
{
    protected $signature = 'pedidos:expire-unpaid {--minutes=30 : Minutes before expiring unpaid orders}';

    protected $description = 'Cancel orders stuck in aguardando_pagamento and restore stock';

    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');
        $cutoff = now()->subMinutes($minutes);

        $orders = Pedido::where('status', OrderStatus::AGUARDANDO_PAGAMENTO)
            ->where('created_at', '<', $cutoff)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No unpaid orders to expire.');

            return self::SUCCESS;
        }

        $auditService = new AuditService;
        $expired = 0;

        foreach ($orders as $pedido) {
            try {
                DB::transaction(function () use ($pedido, $auditService) {
                    // Restore stock
                    foreach ($pedido->itemPedidos as $item) {
                        DB::table('loja_produtos')
                            ->where('loja_id', $pedido->loja_id)
                            ->where('produto_id', $item->produto_id)
                            ->increment('estoque', $item->quantidade_final);
                    }

                    $pedido->update(['status' => OrderStatus::CANCELADO->value]);

                    $auditService->logOrderStatusChanged(
                        $pedido->id,
                        OrderStatus::AGUARDANDO_PAGAMENTO->value,
                        OrderStatus::CANCELADO->value
                    );

                    $auditService->log('order_expired_unpaid', 'pedido', $pedido->id, null, null, [
                        'created_at' => $pedido->created_at->toIso8601String(),
                    ]);
                });

                $expired++;
            } catch (\Throwable $e) {
                Log::error("[ExpireUnpaidOrders] Failed to expire order {$pedido->id}: {$e->getMessage()}");
                $this->error("Failed to expire order {$pedido->id}: {$e->getMessage()}");
            }
        }

        $this->info("Expired {$expired} unpaid order(s).");
        Log::info("[ExpireUnpaidOrders] Expired {$expired} orders.");

        return self::SUCCESS;
    }
}
