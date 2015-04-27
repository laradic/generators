<?php
/**
 * Part of  Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.org
 */
namespace Laradic\Generators;

use Illuminate\Foundation\Application;
use Laradic\Config\Traits\ConfigProviderTrait;
use Laradic\Support\ServiceProvider;

/**
 * {@inheritdoc}
 */
class GeneratorsServiceProvider extends ServiceProvider
{
    use ConfigProviderTrait;

    protected $providers = [
        'Laradic\Generators\Providers\ConsoleServiceProvider'
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        /** @var  \Illuminate\Foundation\Application $app */
        $app = parent::boot();
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        /** @var  \Illuminate\Foundation\Application $app */
        $app = parent::register();
        $this->addConfigComponent('laradic/generators', 'laradic/generators', __DIR__ . '/../resources/config');

    }
}
