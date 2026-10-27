<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // En producción (Railway) el TLS lo termina el proxy, así que las
        // peticiones llegan como http internamente. Forzamos https en las URLs
        // que genera Laravel (enlaces, redirecciones y formularios) para que el
        // navegador no muestre avisos de "sitio no seguro / continuar de todos modos".
        // Se puede desactivar con FORCE_HTTPS=false si hiciera falta en local.
        if (env('FORCE_HTTPS', ! $this->app->environment('local'))) {
            URL::forceScheme('https');
        }
    }
}
