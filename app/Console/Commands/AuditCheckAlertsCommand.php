<?php

namespace App\Console\Commands;

use App\Domains\Auditoria\Models\AuditLog;
use App\Domains\Auth\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuditCheckAlertsCommand extends Command
{
    protected $signature = 'audit:check-alerts';

    protected $description = 'Check operational thresholds and fire alerts for critical events';

    public function handle(): int
    {
        $this->checkBruteForceAttempts();
        $this->checkStalledOrders();
        $this->checkRefusedPayments();
        $this->checkWebhookFailures();
        $this->checkRecurring500Errors();

        return self::SUCCESS;
    }

    /**
     * Alert if 5+ failed logins from same IP in last 10 minutes.
     */
    private function checkBruteForceAttempts(): void
    {
        $threshold = (int) config('alerts.brute_force.threshold', 5);
        $windowMin = (int) config('alerts.brute_force.window_minutes', 10);
        $cooldown = (int) config('alerts.brute_force.cooldown_minutes', 30);
        $window = Carbon::now()->subMinutes($windowMin);

        $suspects = AuditLog::where('action', 'login_failed')
            ->where('created_at', '>=', $window)
            ->selectRaw('ip_address, COUNT(*) as attempts')
            ->groupBy('ip_address')
            ->having('attempts', '>=', $threshold)
            ->get();

        foreach ($suspects as $suspect) {
            if (! $this->alreadyAlerted("brute_force_{$suspect->ip_address}", $cooldown)) {
                $message = "⚠️ Possível ataque de força bruta: {$suspect->attempts} tentativas de login falhadas do IP {$suspect->ip_address}";
                $this->notifyAdmins('brute_force', $message, [
                    'ip_address' => $suspect->ip_address,
                    'attempts' => $suspect->attempts,
                ]);
                Log::channel($this->alertChannel())->warning('[AuditAlert] Brute force detected', [
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
        $hoursThreshold = (int) config('alerts.stalled_orders.hours_threshold', 2);
        $cooldown = (int) config('alerts.stalled_orders.cooldown_minutes', 120);
        $threshold = Carbon::now()->subHours($hoursThreshold);

        $stalledOrders = DB::table('pedidos')
            ->where('status', 'em_entrega')
            ->where('updated_at', '<=', $threshold)
            ->select('id', 'loja_id', 'updated_at')
            ->limit(50)
            ->get();

        foreach ($stalledOrders as $order) {
            if (! $this->alreadyAlerted("stalled_order_{$order->id}", $cooldown)) {
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
        $windowMin = (int) config('alerts.refused_payments.window_minutes', 5);
        $cooldown = (int) config('alerts.refused_payments.cooldown_minutes', 60);
        $lastRun = Carbon::now()->subMinutes($windowMin);

        $refused = DB::table('pagamentos')
            ->where('status', 'recusado')
            ->where('updated_at', '>=', $lastRun)
            ->select('id', 'pedido_id', 'updated_at')
            ->get();

        foreach ($refused as $payment) {
            if (! $this->alreadyAlerted("refused_payment_{$payment->id}", $cooldown)) {
                $message = "💳 Pagamento recusado para pedido #{$payment->pedido_id}.";
                $this->notifyAdmins('payment_refused', $message, [
                    'pagamento_id' => $payment->id,
                    'pedido_id' => $payment->pedido_id,
                ]);
            }
        }
    }

    /**
     * Alert on Asaas webhook failures (invalid token or missing charge).
     */
    private function checkWebhookFailures(): void
    {
        $threshold = (int) config('alerts.webhook_failures.threshold', 3);
        $windowMin = (int) config('alerts.webhook_failures.window_minutes', 5);
        $cooldown = (int) config('alerts.webhook_failures.cooldown_minutes', 30);
        $window = Carbon::now()->subMinutes($windowMin);

        $failureCount = AuditLog::where('action', 'webhook_asaas_invalid_token')
            ->where('created_at', '>=', $window)
            ->count();

        if ($failureCount >= $threshold && ! $this->alreadyAlerted('webhook_failures', $cooldown)) {
            $message = "🔔 {$failureCount} falhas de webhook Asaas nos últimos {$windowMin} minutos — possível problema de integração.";
            $this->notifyAdmins('webhook_failures', $message, [
                'failure_count' => $failureCount,
                'window_minutes' => $windowMin,
            ]);
            Log::channel($this->alertChannel())->error('[AuditAlert] Webhook failures threshold exceeded', [
                'count' => $failureCount,
                'window' => $windowMin,
            ]);
        }
    }

    /**
     * Alert on recurring 500 errors in Laravel log.
     */
    private function checkRecurring500Errors(): void
    {
        $threshold = (int) config('alerts.server_errors.threshold', 5);
        $windowMin = (int) config('alerts.server_errors.window_minutes', 5);
        $cooldown = (int) config('alerts.server_errors.cooldown_minutes', 30);
        $window = Carbon::now()->subMinutes($windowMin);

        $errorCount = AuditLog::where('action', 'server_error_500')
            ->where('created_at', '>=', $window)
            ->count();

        if ($errorCount >= $threshold && ! $this->alreadyAlerted('server_errors_500', $cooldown)) {
            $message = "🔴 {$errorCount} erros 500 nos últimos {$windowMin} minutos — verificar logs do servidor.";
            $this->notifyAdmins('server_errors_500', $message, [
                'error_count' => $errorCount,
                'window_minutes' => $windowMin,
            ]);
            Log::channel($this->alertChannel())->critical('[AuditAlert] Recurring 500 errors', [
                'count' => $errorCount,
                'window' => $windowMin,
            ]);
        }
    }

    /**
     * Get the log channel for alerts (Slack if configured, otherwise default).
     */
    private function alertChannel(): string
    {
        return config('alerts.log_channel', config('logging.default'));
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
                    'id' => Str::uuid()->toString(),
                    'type' => 'App\\Notifications\\OperationalAlert',
                    'notifiable_type' => User::class,
                    'notifiable_id' => $admin->id,
                    'data' => json_encode(array_merge(['type' => $type, 'message' => $message], $data)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Log::channel($this->alertChannel())->info("[AuditAlert] {$type}: {$message}");
        } catch (\Throwable $e) {
            Log::error('[AuditAlert] Failed to send notification: '.$e->getMessage());
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
