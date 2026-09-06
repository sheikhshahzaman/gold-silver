<?php

namespace App\Support;

use App\Models\BuyRequest;
use App\Models\Contact;
use App\Models\HeroSlide;
use App\Models\InventoryItem;
use App\Models\MetalPrice;
use App\Models\NewsTicker;
use App\Models\Order;
use App\Models\Page;
use App\Models\Payment;
use App\Models\PriceMargin;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ScrapeLog;
use App\Models\SerialVerificationAttempt;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\VerifiedSerial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StaffAccess
{
    public const ACTION_VIEW = 'view';
    public const ACTION_CREATE = 'create';
    public const ACTION_EDIT = 'edit';
    public const ACTION_DELETE = 'delete';

    /**
     * @return array<string, string>
     */
    public static function features(): array
    {
        return [
            'dashboard' => 'Dashboard',
            'hero_slides' => 'Hero Slides',
            'pages' => 'Pages',
            'news_ticker' => 'News Ticker',
            'product_categories' => 'Product Categories',
            'products' => 'Products',
            'services' => 'Services',
            'testimonials' => 'Testimonials',
            'price_margins' => 'Gold & Silver Prices',
            'metal_prices' => 'Metal Prices',
            'contact_messages' => 'Contact Messages',
            'buy_requests' => 'Buy Requests',
            'orders' => 'Orders',
            'payments' => 'Payments',
            'inventory_items' => 'Inventory Items',
            'verified_serials' => 'Verification Serial',
            'serial_attempts' => 'Serial Verification Attempts',
            'settings' => 'Settings',
            'site_settings' => 'Site Settings',
            'payment_settings' => 'Payment Settings',
            'sms_settings' => 'Order SMS / Webhook',
            'two_factor_settings' => 'Two-Factor Authentication',
            'scrape_logs' => 'Scrape Logs',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function actions(): array
    {
        return [
            self::ACTION_VIEW => 'View',
            self::ACTION_CREATE => 'Create',
            self::ACTION_EDIT => 'Edit',
            self::ACTION_DELETE => 'Delete',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function permissionOptions(): array
    {
        $options = [];

        foreach (self::features() as $feature => $featureLabel) {
            foreach (self::actions() as $action => $actionLabel) {
                $options["{$feature}.{$action}"] = "{$featureLabel}: {$actionLabel}";
            }
        }

        return $options;
    }

    /**
     * @return array<class-string<Model>, string>
     */
    public static function modelFeatureMap(): array
    {
        return [
            HeroSlide::class => 'hero_slides',
            Page::class => 'pages',
            NewsTicker::class => 'news_ticker',
            ProductCategory::class => 'product_categories',
            Product::class => 'products',
            Service::class => 'services',
            Testimonial::class => 'testimonials',
            PriceMargin::class => 'price_margins',
            MetalPrice::class => 'metal_prices',
            Contact::class => 'contact_messages',
            BuyRequest::class => 'buy_requests',
            Order::class => 'orders',
            Payment::class => 'payments',
            InventoryItem::class => 'inventory_items',
            VerifiedSerial::class => 'verified_serials',
            SerialVerificationAttempt::class => 'serial_attempts',
            Setting::class => 'settings',
            ScrapeLog::class => 'scrape_logs',
            User::class => 'staff_accounts',
        ];
    }

    public static function featureForModel(mixed $model): ?string
    {
        $class = $model instanceof Model ? $model::class : $model;

        return is_string($class) ? (self::modelFeatureMap()[$class] ?? null) : null;
    }

    public static function actionForAbility(string $ability): ?string
    {
        return match ($ability) {
            'viewAny', 'view' => self::ACTION_VIEW,
            'create' => self::ACTION_CREATE,
            'update', 'restore', 'restoreAny', 'replicate', 'reorder' => self::ACTION_EDIT,
            'delete', 'deleteAny', 'forceDelete', 'forceDeleteAny' => self::ACTION_DELETE,
            default => null,
        };
    }

    public static function can(string $feature, string $action, ?User $user = null): bool
    {
        $user ??= Auth::user();

        if (! $user instanceof User || ! $user->isActive()) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (in_array($feature, ['staff_accounts', 'audit_logs'], true)) {
            return false;
        }

        return $user->hasStaffPermission($feature, $action);
    }

    public static function canAny(string $feature, array $actions, ?User $user = null): bool
    {
        foreach ($actions as $action) {
            if (self::can($feature, $action, $user)) {
                return true;
            }
        }

        return false;
    }
}
