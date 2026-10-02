<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\BarangaySetting;
use App\Models\AuditLog;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

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
        // Make $settings available to all views automatically
        View::composer('*', function ($view) {
            $settings = BarangaySetting::first();
            $view->with('settings', $settings);
        });

        // Automatically log when any user logs in
        Event::listen(Login::class, function (Login $event) {
            AuditLog::create([
                'user_id' => $event->user->id,
                'action' => 'USER_LOGIN',
                'description' => "User {$event->user->name} logged into the system.",
                'ip_address' => request()->ip(),
            ]);
        });

        // Automatically log when any user logs out
        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                AuditLog::create([
                    'user_id' => $event->user->id,
                    'action' => 'USER_LOGOUT',
                    'description' => "User {$event->user->name} logged out of the system.",
                    'ip_address' => request()->ip(),
                ]);
            }
        });
    }
}