<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuditService
{
    public static function log(?User $user, string $event, array $meta = [])
    {
        DB::table('audit_logs')->insert([
            'user_id' => $user?->id,
            'event' => $event,
            'meta' => json_encode($meta),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
