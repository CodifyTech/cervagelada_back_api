<?php

namespace App\Domains\Auditoria\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Auth\Models\User;

/**
 * Audit log model - append-only, no updates or deletes allowed.
 */
class AuditLog extends Model
{
    use HasUlids;

    protected $table = 'audit_logs';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Prevent updates - audit logs are append-only.
     */
    public function update(array $attributes = [], array $options = []): bool
    {
        throw new \RuntimeException('Audit logs are append-only and cannot be updated.');
    }

    /**
     * Prevent deletes - audit logs are append-only.
     */
    public function delete(): ?bool
    {
        throw new \RuntimeException('Audit logs are append-only and cannot be deleted.');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
