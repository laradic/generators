<?php

namespace Laradic\Generators\Laravel\EloquentModel\Provider;

use Illuminate\Support\ServiceProvider;
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
        $this->commands([
            GenerateModelCommand::class,
        ]);

        $this->app->tag([
            ExistenceCheckerProcessor::class,
            FieldProcessor::class,
            NamespaceProcessor::class,
            RelationProcessor::class,
            CustomPropertyProcessor::class,
            TableNameProcessor::class,
            CustomPrimaryKeyProcessor::class,
        ], self::PROCESSOR_TAG);

        $this->app->bind(EloquentElementBuilder::class, function ($app) {
            return new EloquentElementBuilder($app->tagged(self::PROCESSOR_TAG));
        });
    }
}
