<?php

namespace Laradic\Generators\Stub;

use Illuminate\View\Factory;
use Illuminate\Events\Dispatcher;
use Illuminate\View\FileViewFinder;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;

class StubGeneratorServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function provides()
    {
        return [
            'laradic.generators.stub',
            'laradic.generators.stub.finder',
            'laradic.generators.stub.factory',
        ];
    }

    public function register()
    {
        $this->registerFinder();
        $this->registerFactory();
        $this->registerStubGenerator();
    }

    protected function registerStubGenerator()
    {
        $this->app->bind('laradic.generators.stub', function (Application $app) {
            return new StubGenerator($app->files, $app[ 'laradic.generators.stub.factory' ]);
        });
    }

    protected function registerFinder()
    {
        $this->app->singleton('laradic.generators.stub.finder', function (Application $app) {
            return new FileViewFinder($app->files, $app->config->get('laradic.generators.stub.paths', [ resource_path('stubs') ]));
        });
    }

    protected function registerFactory()
    {
        $this->app->singleton('laradic.generators.stub.factory', function (Application $app) {
            $cachePath = $app->config->get('laradic.generators.stub.cache_path', storage_path('cache'));
            if ( ! $app->files->isDirectory($cachePath)) {
                $app->files->makeDirectory($cachePath, 0755, true);
            }
            $events   = new Dispatcher();
            $finder   = $app[ 'laradic.generators.stub.finder' ];
            $resolver = new EngineResolver();
            $compiler = new BladeCompiler($app->files, $cachePath);
            $factory  = new Factory($resolver, $finder, $events);
            $factory->setContainer($this->app);
            $factory->share('app', $this->app);
            $resolver->register('blade', function () use ($compiler) {
                return new CompilerEngine($compiler);
            });
            $factory->addExtension('stub', 'blade');
            return $factory;
        });
    }

}