<?php
 /**
 * Part of the Laradic packages.
 * MIT License and copyright information bundled with this package in the LICENSE file.
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
namespace Laradic\Generators\Console;
use Laradic\Generators\Generator;
use Laradic\Console\Command;

/**
 * Class GenerateScaffoldCommand
 *
 * @package     Laradic\Generators
 */
abstract class GenerateCommand extends Command
{

    /**
     * Get the value of generator
     *
     * @return \Laradic\Generators\Generator
     */
    public function getGenerator()
    {
        /** @var \Laradic\Generators\Generator $generator */
        $generator = $this->getLaravel()->make('laradic.generator');
        return $generator;
    }



    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
           # ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
           # ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
