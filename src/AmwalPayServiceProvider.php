<?php

namespace AmwalPay\LaravelPackage;
use App\Http\Controllers\AmwalPayController;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AmwalPayServiceProvider extends ServiceProvider {

    /**
     * Register services.
     *
     * @return void
     */
    public function register() {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'amwalpay');
        $this->publishes([
            __DIR__ . '/../config/amwalpay.php'             => config_path('amwalpay.php'),
            __DIR__ . '/controllers/AmwalPayController.php' => app_path() . '/Http/Controllers/AmwalPayController.php',
            __DIR__ . '/resources/views' => resource_path('views/vendor/amwalpay'),
            __DIR__ . '/resources/js' => public_path('js/vendor/amwalpay'),
                ], 'amwalpay');

        Route::get('amwalpay/callback', [
            'as'   => 'amwalpay.callback', 'uses' => AmwalPayController::class . '@callback'
        ]);

        Route::get('amwalpay/process', [
            'as'   => 'amwalpay.process', 'uses' => AmwalPayController::class . '@process'
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {
        $this->mergeConfigFrom(
                __DIR__ . '/../config/amwalpay.php', 'amwalpay'
        );
    }
}
