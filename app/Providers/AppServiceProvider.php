<?php

namespace App\Providers;

use App\Models\User;
use App\Services\Rates\HybridRatesProvider;
use App\Services\Rates\RatesProvider;
use App\Services\Rates\RemoteRatesProvider;
use App\Support\StaffAccess;
use App\Support\StaffAuditLogger;
use Filament\Resources\Resource;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $priceCalculator = \App\Services\PriceEngine\PriceCalculator::class;
        $priceCacheManager = \App\Services\PriceEngine\PriceCacheManager::class;
        $priceFetcher = \App\Services\PriceEngine\PriceFetcher::class;

        if (class_exists($priceCalculator)) {
            $this->app->singleton($priceCalculator, fn () => new $priceCalculator());
        }

        if (class_exists($priceCacheManager)) {
            $this->app->singleton($priceCacheManager, fn () => new $priceCacheManager());
        }

        if (class_exists($priceFetcher) && class_exists($priceCalculator) && class_exists($priceCacheManager)) {
            $this->app->singleton($priceFetcher, fn ($app) => new $priceFetcher(
                $app->make($priceCalculator),
                $app->make($priceCacheManager),
            ));
        }

        $this->app->singleton(RatesProvider::class, function ($app) {
            $source = config('services.rates.source', 'local');

            if ($source === 'remote') {
                return $app->make(RemoteRatesProvider::class);
            }

            if ($source === 'hybrid') {
                return $app->make(HybridRatesProvider::class);
            }

            $localRatesProvider = \App\Services\Rates\LocalRatesProvider::class;

            if (class_exists($localRatesProvider)) {
                return $app->make($localRatesProvider);
            }

            return $app->make(HybridRatesProvider::class);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Resource::checkPolicyExistence(false);

        $this->configureRateLimiting();

        Gate::before(function (User $user, string $ability, mixed $arguments = null): ?bool {
            if (! $user->isActive()) {
                return false;
            }

            $arguments = is_array($arguments) ? $arguments : [$arguments];
            $model = $arguments[0] ?? null;
            $feature = StaffAccess::featureForModel($model);
            $action = StaffAccess::actionForAbility($ability);

            if (! $feature || ! $action) {
                return null;
            }

            return StaffAccess::can($feature, $action, $user);
        });

        foreach (StaffAccess::modelFeatureMap() as $modelClass => $feature) {
            if (! is_subclass_of($modelClass, Model::class)) {
                continue;
            }

            $modelClass::created(fn (Model $record) => StaffAuditLogger::recordModelChange(
                $feature,
                StaffAccess::ACTION_CREATE,
                $record,
            ));

            $modelClass::updated(fn (Model $record) => StaffAuditLogger::recordModelChange(
                $feature,
                StaffAccess::ACTION_EDIT,
                $record,
            ));

            $modelClass::deleted(fn (Model $record) => StaffAuditLogger::recordModelChange(
                $feature,
                StaffAccess::ACTION_DELETE,
                $record,
            ));
        }
    }

    /**
     * Named API rate limiters.
     *
     * Inline limits such as `throttle:120,1` and `throttle:30,1` all resolve to
     * the SAME cache key (sha1 of "domain|ip"), so reads and writes share one
     * counter. The app's background polling would push that shared counter past
     * 30 within a minute, and every order POST then failed with "Too many
     * attempts" even though it was the user's first one.
     *
     * Named limiters prefix the key with the limiter name, giving reads and
     * writes genuinely separate buckets.
     */
    protected function configureRateLimiting(): void
    {
        // Read traffic: the app polls prices, products and order status, and
        // several devices commonly share one public IP on mobile networks.
        RateLimiter::for('api-read', fn (Request $request) => Limit::perMinute(300)->by($request->ip()));

        // Write traffic: orders, payment proofs, contact and verify. Rare per
        // user, so this stays tight enough to be useful against abuse.
        RateLimiter::for('api-write', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));

        // Server-to-server endpoints must never be starved by public traffic.
        RateLimiter::for('api-internal', fn (Request $request) => Limit::perMinute(120)->by($request->ip()));
    }
}
