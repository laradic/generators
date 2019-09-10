<?php

namespace Laradic\Generators\Laravel\EloquentModel\Processor;

use Laradic\Generators\Core\Elements\NamespaceElement;
use Laradic\Generators\Laravel\EloquentModel\Config;
use Laradic\Generators\Laravel\EloquentModel\Model\EloquentElement;

/**
 * Class NamespaceProcessor
 * @package Krlove\EloquentModelGenerator\Processor
 */
class NamespaceProcessor implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function process(EloquentElement $model, Config $config)
    {
        $model->setNamespace(new NamespaceElement($config->get('namespace')));
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 6;
    }
}
