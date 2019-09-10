<?php

namespace Laradic\Generators\Laravel\EloquentModel\Processor;

use Laradic\Generators\Core\Elements\PropertyElement;
use Laradic\Generators\Core\Elements\DocBlockElement;
use Laradic\Generators\Laravel\EloquentModel\Config;
use Laradic\Generators\Laravel\EloquentModel\Model\EloquentElement;

/**
 * Class CustomPropertyProcessor
 * @package Krlove\EloquentModelGenerator\Processor
 */
class CustomPropertyProcessor implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function process(EloquentElement $model, Config $config)
    {
        if ($config->get('no_timestamps') === true) {
            $pNoTimestamps = new PropertyElement('timestamps', 'public', false);
            $pNoTimestamps->setDocBlock(
                new DocBlockElement('Indicates if the Element should be timestamped.', '', '@var bool')
            );
            $model->addProperty($pNoTimestamps);
        }

        if ($config->has('date_format')) {
            $pDateFormat = new PropertyElement('dateFormat', 'protected', $config->get('date_format'));
            $pDateFormat->setDocBlock(
                new DocBlockElement('The storage format of the Element\'s date columns.', '', '@var string')
            );
            $model->addProperty($pDateFormat);
        }

        if ($config->has('connection')) {
            $pConnection = new PropertyElement('connection', 'protected', $config->get('connection'));
            $pConnection->setDocBlock(
                new DocBlockElement('The connection name for the Element.', '', '@var string')
            );
            $model->addProperty($pConnection);
        }
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 5;
    }
}
