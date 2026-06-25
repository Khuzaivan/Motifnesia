<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Order;
use App\Models\OrderReview;
use App\Models\Produk;
use App\Models\ProductReturn;
use App\Observers\OrderObserver;
use App\Observers\ReviewObserver;
use App\Observers\ProductObserver;
use App\Observers\ProductReturnObserver;

use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers untuk auto-generate notifications
        Order::observe(OrderObserver::class);
        OrderReview::observe(ReviewObserver::class);
        Produk::observe(ProductObserver::class);
        ProductReturn::observe(ProductReturnObserver::class);

        // Register Admin RBAC Gates
        Gate::define('is-owner', function (User $user) {
            return $user->role === 'admin' && ($user->admin_role === 'owner' || is_null($user->admin_role));
        });

        Gate::define('is-finance', function (User $user) {
            return $user->role === 'admin' && in_array($user->admin_role, ['owner', 'finance']);
        });

        Gate::define('is-kasir', function (User $user) {
            return $user->role === 'admin' && in_array($user->admin_role, ['owner', 'kasir']);
        });

        Gate::define('is-gudang', function (User $user) {
            return $user->role === 'admin' && in_array($user->admin_role, ['owner', 'gudang']);
        });

        Gate::define('is-supplier', function (User $user) {
            return $user->role === 'supplier';
        });
    }
}
