<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Record an admin action in the audit log.
     *
     * @param  string       $action     Short action identifier e.g. 'update_payment_status'
     * @param  Model|string $model      The Eloquent model instance or class name
     * @param  int|null     $modelId    The model primary key (auto-detected from $model if it's an instance)
     * @param  array        $details    Arbitrary key-value context (before/after, reason, etc.)
     */
    public static function log(string $action, $model, ?int $modelId = null, array $details = []): AuditLog
    {
        $modelType = $model instanceof Model ? get_class($model) : (string) $model;
        $modelId = $modelId ?? ($model instanceof Model ? $model->getKey() : null);

        return AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'model_type' => $modelType,
            'model_id'   => $modelId,
            'details'    => !empty($details) ? $details : null,
            'ip_address' => Request::ip(),
            'created_at' => now(),
        ]);
    }
}
