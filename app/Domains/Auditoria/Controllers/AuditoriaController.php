<?php

namespace App\Domains\Auditoria\Controllers;

use App\Domains\Auditoria\Models\AuditLog;
use App\Domains\Shared\Controller\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditoriaController extends BaseController
{
    public function __construct()
    {
        $this->setACL('audit', [
            'list' => ['audit.read'],
        ]);
        parent::__construct();
    }

    /**
     * GET /api/audit-logs
     * List audit logs with filters. Pagination is mandatory.
     */
    public function index(Request $request, ?\Closure $builderCallback = null): JsonResponse
    {
        $query = AuditLog::with('user:id,name,email')
            ->orderByDesc('created_at');

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->input('entity_type'));
        }
        if ($request->filled('de')) {
            $query->where('created_at', '>=', \Carbon\Carbon::parse($request->input('de'))->startOfDay());
        }
        if ($request->filled('ate')) {
            $query->where('created_at', '<=', \Carbon\Carbon::parse($request->input('ate'))->endOfDay());
        }

        $perPage = min((int) $request->input('per_page', 20), 100);
        $paginated = $query->paginate($perPage);

        return response()->json([
            'data' => $paginated->items(),
            'total' => $paginated->total(),
            'page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
        ]);
    }

    /**
     * GET /api/audit-logs/{id}
     * Show a single audit log entry with diff.
     */
    public function show(string $id): JsonResponse
    {
        $log = AuditLog::with('user:id,name,email')->findOrFail($id);

        $diff = null;
        if ($log->old_values !== null && $log->new_values !== null) {
            $diff = $this->computeDiff($log->old_values, $log->new_values);
        }

        return response()->json([
            'data' => $log,
            'diff' => $diff,
        ]);
    }

    /**
     * Compute a simple key-by-key diff of old vs new values.
     */
    private function computeDiff(array $old, array $new): array
    {
        $allKeys = array_unique(array_merge(array_keys($old), array_keys($new)));
        $diff = [];

        foreach ($allKeys as $key) {
            $oldVal = $old[$key] ?? null;
            $newVal = $new[$key] ?? null;

            if ($oldVal !== $newVal) {
                $diff[$key] = ['old' => $oldVal, 'new' => $newVal];
            }
        }

        return $diff;
    }
}
