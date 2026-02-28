<?php

namespace App\Providers;

use App\Contracts\AIProvider;
use App\Services\AI\OpenAIProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use OpenAI;
use OpenAI\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            // @link https://github.com/barryvdh/laravel-ide-helper
            if (class_exists(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class)) {
                $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            }
        }

        $this->app->singleton(Client::class, function () {
            return OpenAI::client(config('openai.api_key'));
        });

        $this->app->bind(AIProvider::class, OpenAIProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // @link https://laravel.com/docs/9.x/eloquent#enabling-eloquent-strict-mode
        Model::shouldBeStrict();
    }
}
