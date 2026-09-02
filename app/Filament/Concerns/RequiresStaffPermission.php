<?php

namespace App\Filament\Concerns;

use App\Support\StaffAccess;

trait RequiresStaffPermission
{
    public static function canAccess(): bool
    {
        return StaffAccess::can(static::$staffFeature, StaffAccess::ACTION_VIEW);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    protected function ensureCanEdit(): void
    {
        abort_unless(StaffAccess::can(static::$staffFeature, StaffAccess::ACTION_EDIT), 403);
    }
}
