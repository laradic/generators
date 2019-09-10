<?php

namespace Laradic\Generators\Laravel\EloquentModel;

use Laradic\Generators\Laravel\EloquentModel\Model\EloquentElement;
use Laradic\Generators\Laravel\EloquentModel\Exception\GeneratorException;
use Laradic\Generators\Laravel\EloquentModel\Processor\ProcessorInterface;

/**
 * Class EloquentElementBuilder
 * @package Krlove\EloquentModelGenerator
 */
class EloquentElementBuilder
{
    /**
     * @var ProcessorInterface[]
     */
    protected $processors;

    /**
     * EloquentElementBuilder constructor.
     * @param ProcessorInterface[]|\IteratorAggregate $processors
     */
    public function __construct($processors)
    {
        if ($processors instanceof \IteratorAggregate) {
            $this->processors = iterator_to_array($processors);
        } else {
            $this->processors = $processors;
        }
    }

    /**
     * @param Config $config
     * @return EloquentElement
     * @throws GeneratorException
     */
    public function createElement(Config $config)
    {
        $model = new EloquentElement();

        $this->prepareProcessors();

        foreach ($this->processors as $processor) {
            $processor->process($model, $config);
        }

        return $model;
    }

    /**
     * Sort processors by priority
     */
    protected function prepareProcessors()
    {
        usort($this->processors, function (ProcessorInterface $one, ProcessorInterface $two) {
            if ($one->getPriority() == $two->getPriority()) {
                return 0;
            }

            return $one->getPriority() < $two->getPriority() ? 1 : -1;
        });
    }
}
