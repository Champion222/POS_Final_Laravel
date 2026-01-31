<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Share notification data globally
        View::composer('*', function ($view) {
            // Get products with stock < 10, including image data
            $notifications = Product::where('qty', '<', 10)
                                    ->select('id', 'name', 'qty', 'image')
                                    ->take(5)
                                    ->get();
                                    
            $view->with('notifications', $notifications);
        });
    }
}