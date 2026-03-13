<?php

namespace App\Domains\Auditoria\Services;

use App\Domains\Auditoria\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuditService
{
    /**
     * Register an audit event.
     *
     * @param  string  $action  e.g. 'login', 'pedido.status_changed', 'user.created'
     * @param  string|null  $entityType  e.g. 'pedido', 'user', 'loja'
     * @param  array  $metadata  Extra context
     */
    public function log(
        string $action,
        ?string $entityType = null,
        ?string $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        array $metadata = []
    ): void {
        try {
            /** @var Request $request */
            $request = app(Request::class);
            $userId = auth()->id();

            AuditLog::create([
                'user_id' => $userId,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'metadata' => array_merge($metadata, [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                ]),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Audit logging should never break application flow
            Log::error('[AuditService] Failed to write audit log: '.$e->getMessage(), [
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
            ]);
        }
    }

    /**
     * Log login success event.
     */
    public function logLogin(string $userId, string $email): void
    {
        $this->log('login', 'user', $userId, null, null, ['email' => $email]);
    }

    /**
     * Log login failure event.
     */
    public function logLoginFailed(string $email): void
    {
        $this->log('login_failed', 'user', null, null, null, ['email' => $email]);
    }

    /**
     * Log logout event.
     */
    public function logLogout(string $userId): void
    {
        $this->log('logout', 'user', $userId);
    }

    /**
     * Log order status change event.
     */
    public function logOrderStatusChanged(string $orderId, string $oldStatus, string $newStatus): void
    {
        $this->log(
            'order_status_changed',
            'pedido',
            $orderId,
            ['status' => $oldStatus],
            ['status' => $newStatus]
        );
    }

    /**
     * Log payment received event.
     */
    public function logPaymentReceived(string $orderId, string $paymentId, float $amount): void
    {
        $this->log('payment_received', 'pedido', $orderId, null, null, [
            'payment_id' => $paymentId,
            'amount' => $amount,
        ]);
    }

    /**
     * Log payment event (recusado, estornado, cancelado).
     */
    public function logPaymentEvent(string $action, string $orderId, array $metadata = []): void
    {
        $this->log($action, 'pedido', $orderId, null, null, $metadata);
    }

    /**
     * Log generic entity creation.
     */
    public function logCreated(string $entityType, string $entityId, array $newValues = []): void
    {
        $this->log("{$entityType}.created", $entityType, $entityId, null, $newValues);
    }

    /**
     * Log generic entity update.
     */
    public function logUpdated(string $entityType, string $entityId, array $oldValues = [], array $newValues = []): void
    {
        $this->log("{$entityType}.updated", $entityType, $entityId, $oldValues, $newValues);
    }

    /**
     * Log generic entity deletion.
     */
    public function logDeleted(string $entityType, string $entityId, array $oldValues = []): void
    {
        $this->log("{$entityType}.deleted", $entityType, $entityId, $oldValues, null);
    }

    /**
     * Log role/permission change.
     */
    public function logPermissionChanged(string $userId, string $action, array $metadata = []): void
    {
        $this->log($action, 'user', $userId, null, null, $metadata);
    }
}
