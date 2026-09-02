<?php

namespace App\Support;

use App\Models\StaffAuditLog;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StaffAuditLogger
{
    public static function record(string $feature, string $action, ?Model $record = null, array $changes = []): void
    {
        if (! self::shouldRecord()) {
            return;
        }

        $user = Auth::user();

        if (! $user instanceof User) {
            return;
        }

        StaffAuditLog::create([
            'user_id' => $user->id,
            'staff_name' => $user->name,
            'staff_email' => $user->email,
            'feature' => $feature,
            'action' => $action,
            'auditable_type' => $record?->getMorphClass(),
            'auditable_id' => $record?->getKey(),
            'auditable_label' => $record ? self::labelFor($record) : null,
            'ip_address' => request()->ip(),
            'user_agent' => Str::limit((string) request()->userAgent(), 500, ''),
            'changes' => self::cleanChanges($changes, $record),
            'created_at' => now(),
        ]);
    }

    public static function recordModelChange(string $feature, string $action, Model $record): void
    {
        $changes = match ($action) {
            StaffAccess::ACTION_CREATE => $record->getAttributes(),
            StaffAccess::ACTION_EDIT => $record->getChanges(),
            StaffAccess::ACTION_DELETE => $record->getOriginal(),
            default => [],
        };

        self::record($feature, $action, $record, $changes);
    }

    private static function shouldRecord(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        $user = Auth::user();

        if (! $user instanceof User || (! $user->isAdmin() && ! $user->isStaff())) {
            return false;
        }

        if (request()->is('admin*')) {
            return true;
        }

        $referer = (string) request()->headers->get('referer');

        return str_contains($referer, '/admin');
    }

    private static function labelFor(Model $record): string
    {
        foreach (['name', 'title', 'order_number', 'email', 'key', 'serial_number', 'reference_number'] as $field) {
            if (filled($record->{$field} ?? null)) {
                return (string) $record->{$field};
            }
        }

        return class_basename($record) . ' #' . $record->getKey();
    }

    private static function cleanChanges(array $changes, ?Model $record): array
    {
        $hidden = ['password', 'remember_token', 'updated_at'];

        foreach ($hidden as $field) {
            unset($changes[$field]);
        }

        if ($record instanceof Setting && self::isSensitiveSetting((string) $record->key)) {
            $changes['value'] = '[hidden]';
        }

        return $changes;
    }

    private static function isSensitiveSetting(string $key): bool
    {
        foreach (['password', 'token', 'secret', 'api_key', 'auth'] as $sensitiveWord) {
            if (str_contains(strtolower($key), $sensitiveWord)) {
                return true;
            }
        }

        return false;
    }
}
