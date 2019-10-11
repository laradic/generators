<?php

namespace Laradic\Generators;

use Illuminate\Support\ServiceProvider;
use Laradic\Generators\Stub\StubGeneratorServiceProvider;

class GeneratorsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/config/laradic.generators.php', 'laradic.generators');
        $this->app->register(StubGeneratorServiceProvider::class);
    }

}