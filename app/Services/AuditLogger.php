<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function log(string $action, mixed $target = null, array $metadata = [], ?int $actorUserId = null): AuditLog
    {
        $targetType = null;
        $targetId = null;

        if ($target instanceof Model) {
            $targetType = $target::class;
            $targetId = (int) $target->getKey();
        } elseif (is_array($target)) {
            $targetType = $target['type'] ?? null;
            $targetId = isset($target['id']) ? (int) $target['id'] : null;
        }

        return AuditLog::create([
            'actor_user_id' => $actorUserId ?? Auth::id(),
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }
}
