<?php

namespace Laradic\Generators\Laravel\EloquentModel\Processor;

use Laradic\Generators\Laravel\EloquentModel\Config;
use Laradic\Generators\Laravel\EloquentModel\Model\EloquentElement;

/**
 * Interface ProcessorInterface
 * @package Krlove\EloquentModelGenerator\Processor
 */
interface ProcessorInterface
{
    /**
     * @param EloquentElement $model
     * @param Config $config
     */
    public function process(EloquentElement $model, Config $config);

    /**
     * @return int
     */
    public function getPriority();
}
