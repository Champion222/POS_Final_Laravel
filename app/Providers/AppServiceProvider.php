<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Models\Category;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Sale;
use App\Models\User;
use App\Observers\ActivityObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        View::composer('*', function ($view) {
            $notifications = Product::where('qty', '<', 10)
                ->select('id', 'name', 'qty', 'image')
                ->take(5)
                ->get();

            $view->with('notifications', $notifications);
        });

        $this->registerActivityObservers();
    }

    private function registerActivityObservers(): void
    {
        $auditableModels = [
            User::class,
            Product::class,
            Category::class,
            Employee::class,
            Position::class,
            Promotion::class,
            Attendance::class,
            Sale::class,
        ];

        foreach ($auditableModels as $auditableModel) {
            $auditableModel::observe(ActivityObserver::class);
        }
    }
}
