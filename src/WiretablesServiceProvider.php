<?php

namespace EduLazaro\Wiretables;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class WiretablesServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'wiretables');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'wiretables');

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/wiretables'),
        ], 'wiretables-lang');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/wiretables'),
        ], 'wiretables-views');

        $this->publishes([
            __DIR__.'/../resources/css' => public_path('vendor/wiretables/css'),
        ], 'wiretables-assets');

        // Anonymous components: <x-wiretable> is components/wiretable/index.blade.php and
        // <x-wiretable.th> its th.blade.php, so the pieces read as one family.
        $this->callAfterResolving(BladeCompiler::class, function (BladeCompiler $blade) {
            $blade->anonymousComponentPath(__DIR__.'/../resources/views/components');
        });
    }
}
