<?php

namespace App\Providers;
use App\Models\User;
use App\Models\Notification;
use App\Models\UserInfo;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator; 


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
        // Ensure tenant resolver runs for web requests
        $this->app->make(\Illuminate\Routing\Router::class)
            ->pushMiddlewareToGroup('web', \App\Http\Middleware\ResolveTenant::class);

        View::composer('layout.footer', function ($view) {

            if (!Auth::check()) {
                return;
            }

            $authId = Auth::id();

            $users = User::where('id', '!=', $authId)
                ->with('userinfo', 'department')
                ->withCount([
                    'receivedMessages as unread_count' => function ($q) use ($authId) {
                        $q->where('receiver_id', $authId)
                        ->where('is_read', false);
                    }
                ])
                ->get();

            $view->with('users', $users);
        });

        // 🔔 Header data
        View::composer('layout.header', function ($view) {

            if (!Auth::check()) {
                return;
            }

            $notifications = Notification::where('created_by', auth()->id())->whereNull('read_at')->with('userinfo', 'user')->latest()->get();

            $view->with('notifications', $notifications);
        });

         Paginator::useBootstrap();
    }
}
