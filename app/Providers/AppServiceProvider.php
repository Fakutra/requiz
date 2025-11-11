<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Contact;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

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
        View::composer('*', function ($view) {
            $footerContacts = Cache::remember('footer_contacts', 600, function () {
                return \App\Models\Contact::where('is_active', true)
                    ->orderByDesc('updated_at')
                    ->limit(3)
                    ->get();
            });

            $view->with('footerContacts', $footerContacts);
        });
    }
}
