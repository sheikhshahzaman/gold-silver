<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\StaffAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffCanAccessAdminFeature
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User || $user->isAdmin()) {
            return $next($request);
        }

        if (! $user->isStaff() || ! $user->isActive()) {
            abort(403);
        }

        $path = trim(preg_replace('#^admin/?#', '', $request->path()) ?? '', '/');

        if ($path === '' || $path === 'login' || $path === 'logout') {
            return $next($request);
        }

        $feature = $this->featureForPath($path);

        if (! $feature) {
            return $next($request);
        }

        abort_unless(StaffAccess::can($feature, $this->actionForPath($path), $user), 403);

        return $next($request);
    }

    private function featureForPath(string $path): ?string
    {
        $segment = explode('/', $path)[0] ?? '';

        return [
            'hero-slides' => 'hero_slides',
            'pages' => 'pages',
            'news-tickers' => 'news_ticker',
            'product-categories' => 'product_categories',
            'products' => 'products',
            'services' => 'services',
            'testimonials' => 'testimonials',
            'price-margins' => 'price_margins',
            'metal-prices' => 'metal_prices',
            'contacts' => 'contact_messages',
            'orders' => 'orders',
            'payments' => 'payments',
            'inventory-items' => 'inventory_items',
            'verified-serials' => 'verified_serials',
            'serial-verification-attempts' => 'serial_attempts',
            'settings' => 'settings',
            'site-settings' => 'site_settings',
            'payment-settings' => 'payment_settings',
            'order-sms-settings' => 'sms_settings',
            'two-factor-settings' => 'two_factor_settings',
            'scrape-logs' => 'scrape_logs',
            'staff-accounts' => 'staff_accounts',
            'staff-audit-logs' => 'audit_logs',
        ][$segment] ?? null;
    }

    private function actionForPath(string $path): string
    {
        if (str_ends_with($path, '/create') || str_contains($path, '/create/')) {
            return StaffAccess::ACTION_CREATE;
        }

        if (str_ends_with($path, '/edit') || str_contains($path, '/edit/')) {
            return StaffAccess::ACTION_EDIT;
        }

        return StaffAccess::ACTION_VIEW;
    }
}
