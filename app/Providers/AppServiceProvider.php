<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale('id');

        Blade::directive('date', function ($expression) {
            return "<?php echo \Carbon\Carbon::parse($expression)->translatedFormat('d F Y | H:i'); ?>";
        });

        Paginator::useBootstrap();

        Blade::directive('imageToBase64', function ($path) {
            return "<?php
                \$path = $path;
                \$options = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'cafile' => base_path('cert.pem'), // Sesuaikan dengan lokasi yang Anda gunakan
                    ],
                ];

                \$image = file_get_contents(public_path(\$path), false, stream_context_create(\$options));
                \$base64 = base64_encode(\$image);
                echo 'data:image/png;base64,' . \$base64;
            ?>";
        });


        URL::forceScheme('https');
    }
}
