<?php

declare(strict_types=1);

namespace MyDaniel\Paseto;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use InvalidArgumentException;
use MyDaniel\Paseto\Commands\GeneratePasetoKeyCommand;
use MyDaniel\Paseto\Contracts\BlacklistContract;
use MyDaniel\Paseto\Guard\PasetoGuard;
use MyDaniel\Paseto\Contracts\PasetoTokenParserContract;

/**
 * The service provider for the Paseto package.
 */
class PasetoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/paseto.php' => config_path('paseto.php'),
        ], 'config');

        Auth::extend('paseto', function (Application $app, string $name, array $config): PasetoGuard {
            $provider = Auth::createUserProvider($config['provider']);
            $parser = $app[PasetoTokenParserContract::class];
            $blacklist = $app[BlacklistContract::class];

            return new PasetoGuard($provider, $parser, $blacklist);
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                GeneratePasetoKeyCommand::class,
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/paseto.php', 'paseto');

        $this->app->singleton(Paseto::class, function ($app) {
            $secretKey = $app['config']['paseto.secret_key'];
            if (!$secretKey) {
                throw new InvalidArgumentException('Paseto Secret Key is not set.');
            }

            return new Paseto($secretKey);
        });

        $this->app->singleton(PasetoTokenParserContract::class, function ($app) {
            $paseto = $app[Paseto::class];
            $parser = $paseto->parser();

            if ($issuer = $app['config']['paseto.issuer']) {
                $parser->setIssuedBy($issuer);
            }

            if ($audience = $app['config']['paseto.audience']) {
                $parser->setForAudience($audience);
            }

            return $parser;
        });

        $this->app->singleton(BlacklistContract::class, function ($app) {
            $cacheStore = $app['config']['paseto.blacklist_cache_store'];
            $cache = $app[CacheFactory::class]->store($cacheStore);

            return new Blacklist($cache);
        });
    }
}
