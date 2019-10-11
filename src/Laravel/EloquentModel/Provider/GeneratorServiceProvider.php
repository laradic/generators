<?php

namespace Laradic\Generators\Laravel\EloquentModel\Provider;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Laradic\Generators\Laravel\EloquentModel\EloquentElementBuilder;
use Laradic\Generators\Laravel\EloquentModel\Processor\FieldProcessor;
use Laradic\Generators\Laravel\EloquentModel\Processor\RelationProcessor;
use Laradic\Generators\Laravel\EloquentModel\Command\GenerateModelCommand;
use Laradic\Generators\Laravel\EloquentModel\Processor\NamespaceProcessor;
use Laradic\Generators\Laravel\EloquentModel\Processor\TableNameProcessor;
use Laradic\Generators\Laravel\EloquentModel\Processor\CustomPropertyProcessor;
use Laradic\Generators\Laravel\EloquentModel\Processor\CustomPrimaryKeyProcessor;
use Laradic\Generators\Laravel\EloquentModel\Processor\ExistenceCheckerProcessor;

/**
 * Class GeneratorServiceProvider
 *
 * @package Krlove\EloquentModelGenerator\Provider
 */
class GeneratorServiceProvider extends ServiceProvider
{
    const PROCESSOR_TAG = 'eloquent_model_generator.processor';

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->registerCommands();
        $this->registerProcessors(self::PROCESSOR_TAG);
        $this->registerBuilder(self::PROCESSOR_TAG);
    }

    protected function registerBuilder($tag)
    {
        $this->app->bind(EloquentElementBuilder::class, function (Application $app) use ($tag) {
            return new EloquentElementBuilder($app->tagged($tag));
        });
    }

    protected function registerProcessors($tag)
    {
        $this->app->tag([
            ExistenceCheckerProcessor::class,
            FieldProcessor::class,
            NamespaceProcessor::class,
            RelationProcessor::class,
            CustomPropertyProcessor::class,
            TableNameProcessor::class,
            CustomPrimaryKeyProcessor::class,
        ], $tag);
    }

    protected function registerCommands()
    {
        $this->commands([
            GenerateModelCommand::class,
        ]);
    }
}
