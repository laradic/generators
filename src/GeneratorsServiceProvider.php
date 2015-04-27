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

    public function boot()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = parent::boot();
    }

    public function register()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = parent::register();
        $app->bind('laradic.generator', function (Application $app)
        {
            return new Generator($app->make('files'));
        });

        $this->addConfigComponent('laradic/generator', 'laradic/generator', realpath(__DIR__ . '/../resources/config'));
        if ( $this->app->runningInConsole() )
        {
            $app->register('Laradic\Generators\Providers\ConsoleServiceProvider');
        }
    }
}
