<?php
/**
 * Part of the Laradic packages.
 * MIT License and copyright information bundled with this package in the LICENSE file.
 *
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
namespace Laradic\Generators;

use Illuminate\Foundation\Application;
use Illuminate\View\Engines\CompilerEngine;
use Laradic\Config\Traits\ConfigProviderTrait;
use Laradic\Support\ServiceProvider;

/**
 * Class GeneratorsServiceProvider
 *
 * @package     Laradic\Generators
 */
class GeneratorsServiceProvider extends ServiceProvider
{
    use ConfigProviderTrait;

    protected $providers = [ 'Laradic\Generators\Providers\ConsoleServiceProvider' ];

    protected $aliases = [ 'Generator' ];

    protected $provides = [ 'laradic.generator' ];

    public function boot()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = parent::boot();
    }

    public function register()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app    = parent::register();
        $config = $this->addConfigComponent('laradic/generator', 'laradic/generator', realpath(__DIR__ . '/../resources/config'));

        $this->registerEngine();
        $this->registerExtensions($config[ 'extensions' ]);

        $app->bind('Laradic\Generators\Generator', function (Application $app)
        {
            return new Generator($app->make('files'), $app->make('view'));
        });
        $app->bind('laradic.generator', 'Laradic\Generators\Generator');
    }

    protected function registerEngine()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app      = $this->app;
        $resolver = $app->make('view.engine.resolver');
        $resolver->register('stub', function () use ($app)
        {
            $compiler = $app->make('blade.compiler');

            return new CompilerEngine($compiler);
        });
        $app->make('view')->addExtension('stub', 'stub');
    }

    protected function registerExtensions($extensions)
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app  = $this->app;
        $view = $app->make('view');
        foreach ( $extensions as $ex )
        {
            $view->addExtension("$ex.stub", 'stub');
        }
    }
}
