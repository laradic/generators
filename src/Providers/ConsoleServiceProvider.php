<?php
/**
 * Part of Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.org
 */
namespace Laradic\Generators\Providers;

use Laradic\Console\AggregateConsoleProvider;

/**
 * This is the ConsoleServiceProvider class.
 *
 * @package                Laradic\Generators
 * @version                1.0.0
 * @author                  Robin Radic
 * @license                MIT License
 * @copyright            2015, Robin Radic
 * @link                      https://github.com/robinradic
 */
class ConsoleServiceProvider extends AggregateConsoleProvider
{
    protected $namespace = 'Laradic\Generators\Console';
    protected $commands = [
        'GeneratorsList' => 'laradic.generators.commands.list'
    ];
}
