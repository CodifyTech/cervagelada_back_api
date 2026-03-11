<?php

namespace App\Console\Commands;

use App\Domains\Auditoria\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Domains\Auth\Models\User;

class AuditCheckAlertsCommand extends Command
{
    protected $signature = 'audit:check-alerts';
    protected $description = 'Check operational thresholds and fire alerts for critical events';

    public function handle(): int
    {
        $this->checkBruteForceAttempts();
        $this->checkStalledOrders();
        $this->checkRefusedPayments();

        return self::SUCCESS;
    }

    /**
     * Alert if 5+ failed logins from same IP in last 10 minutes.
     */
    private function checkBruteForceAttempts(): void
    {
        $threshold = 5;
        $window = Carbon::now()->subMinutes(10);

        $suspects = AuditLog::where('action', 'login_failed')
            ->where('created_at', '>=', $window)
            ->selectRaw('ip_address, COUNT(*) as attempts')
            ->groupBy('ip_address')
            ->having('attempts', '>=', $threshold)
            ->get();

        foreach ($suspects as $suspect) {
            $alreadyAlerted = $this->alreadyAlerted("brute_force_{$suspect->ip_address}", 30);
            if (!$alreadyAlerted) {
                $message = "⚠️ Possível ataque de força bruta: {$suspect->attempts} tentativas de login falhadas do IP {$suspect->ip_address}";
                $this->notifyAdmins('brute_force', $message, [
                    'ip_address' => $suspect->ip_address,
                    'attempts' => $suspect->attempts,
                ]);
                Log::warning('[AuditAlert] Brute force detected', [
                    'ip' => $suspect->ip_address,
                    'attempts' => $suspect->attempts,
                ]);
            }
        }
    }

    /**
     * Alert if orders are stuck in 'em_entrega' for more than 2 hours.
     */
    private function checkStalledOrders(): void
    {
        $threshold = Carbon::now()->subHours(2);

        $stalledOrders = DB::table('pedidos')
            ->where('status', 'em_entrega')
            ->where('updated_at', '<=', $threshold)
            ->select('id', 'loja_id', 'updated_at')
            ->limit(50)
            ->get();

        foreach ($stalledOrders as $order) {
            $alreadyAlerted = $this->alreadyAlerted("stalled_order_{$order->id}", 120);
            if (!$alreadyAlerted) {
                $hoursAgo = Carbon::parse($order->updated_at)->diffForHumans();
                $message = "⏰ Pedido #{$order->id} em status 'em_entrega' desde {$hoursAgo} sem atualização.";
                $this->notifyAdmins('stalled_order', $message, [
                    'pedido_id' => $order->id,
                    'loja_id' => $order->loja_id,
                    'updated_at' => $order->updated_at,
                ]);
            }
        }
    }

    /**
     * Alert on recently refused payments.
     */
    private function checkRefusedPayments(): void
    {
        $lastRun = Carbon::now()->subMinutes(5);

        $refused = DB::table('pagamentos')
            ->where('status', 'recusado')
            ->where('updated_at', '>=', $lastRun)
            ->select('id', 'pedido_id', 'updated_at')
            ->get();

        foreach ($refused as $payment) {
            $alreadyAlerted = $this->alreadyAlerted("refused_payment_{$payment->id}", 60);
            if (!$alreadyAlerted) {
                $message = "💳 Pagamento recusado para pedido #{$payment->pedido_id}.";
                $this->notifyAdmins('payment_refused', $message, [
                    'pagamento_id' => $payment->id,
                    'pedido_id' => $payment->pedido_id,
                ]);
            }
        }
    }

    /**
     * Send a database notification to all admin users.
     */
    private function notifyAdmins(string $type, string $message, array $data = []): void
    {
        try {
            $admins = User::whereHas('roles', function ($q) {
                $q->where('nome', 'admin');
            })->get();

            foreach ($admins as $admin) {
                $admin->notifications()->create([
                    'id' => \Illuminate\Support\Str::uuid()->toString(),
                    'type' => 'App\\Notifications\\OperationalAlert',
                    'notifiable_type' => User::class,
                    'notifiable_id' => $admin->id,
                    'data' => json_encode(array_merge(['type' => $type, 'message' => $message], $data)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('[AuditAlert] Failed to send notification: ' . $e->getMessage());
        }
    }

    /**
     * Check if an alert for this key was already sent within the given window (minutes).
     */
    private function alreadyAlerted(string $key, int $minutes): bool
    {
        return DB::table('notifications')
            ->where('type', 'App\\Notifications\\OperationalAlert')
            ->whereRaw("JSON_EXTRACT(data, '$.type') = ?", [$key])
            ->where('created_at', '>=', Carbon::now()->subMinutes($minutes))
            ->exists();
    }
}
