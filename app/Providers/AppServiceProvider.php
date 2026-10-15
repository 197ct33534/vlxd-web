<?php

namespace App\Providers;

use App\Models\Order;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        Paginator::useTailwind();

        View::composer('components.sidebar', function ($view) {
            $pendingOrders = 0;
            $user = Auth::user();
            if ($user && $user->isAdmin()) {
                $pendingOrders = Order::where('status', 'pending_confirm')->count();
            }
            $view->with('pendingOrdersCount', $pendingOrders);
        });
    }
}
