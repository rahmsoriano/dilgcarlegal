<?php

namespace App\Providers;

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
        View::composer('layouts.admin', function ($view) {
            if (Auth::check()) {
                $view->with('sidebarConversations', Auth::user()->conversations()
                    ->with('messages') // Eager load messages
                    ->orderByDesc('last_message_at')
                    ->orderByDesc('id')
                    ->limit(10)
                    ->get());
            }
        });
    }
}
