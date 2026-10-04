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
        // Make $settings (saved row or null) and $brgy (saved row or defaults,
        // used for the barangay name shown across the app) available to all views
        View::composer('*', function ($view) {
            $view->with('settings', BarangaySetting::stored());
            $view->with('brgy', BarangaySetting::current());
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