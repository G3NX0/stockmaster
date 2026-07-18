<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Item;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $lowStockCount = Item::whereColumn('stok_barang', '<=', 'min_stock')->count();
                $view->with('globalLowStockCount', $lowStockCount);
            }
        });
    }
}
