<?php

namespace App\Providers;

use App\Models\Document;
use App\Models\Image;
use App\Observers\DocumentObserver;
use App\Observers\ImageObserver;
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
        Image::observe(ImageObserver::class);
        Document::observe(DocumentObserver::class);
    }
}
