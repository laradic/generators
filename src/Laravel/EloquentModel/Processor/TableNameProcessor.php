<?php

namespace Laradic\Generators\Laravel\EloquentModel\Processor;

use Laradic\Generators\Core\Elements\UseElement;
use Laradic\Generators\Laravel\EloquentModel\Config;
use Laradic\Generators\Core\Elements\DocBlockElement;
use Laradic\Generators\Core\Elements\PropertyElement;
use Laradic\Generators\Core\Elements\ClassNameElement;
use Laradic\Generators\Laravel\EloquentModel\Helper\EmgHelper;
use Laradic\Generators\Laravel\EloquentModel\Model\EloquentElement;


/**
 * Class TableNameProcessor
 *
 * @package Krlove\EloquentModelGenerator\Processor
 */
class TableNameProcessor implements ProcessorInterface
{
    /**
     * @var EmgHelper
     */
    protected $helper;

    /**
     * TableNameProcessor constructor.
     *
     * @param EmgHelper $helper
     */
    public function __construct(EmgHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @inheritdoc
     */
    public function process(EloquentElement $model, Config $config)
    {
        $className     = $config->get('class_name');
        $baseClassName = $config->get('base_class_name');
        $tableName     = $config->get('table_name');

        $model->setName(new ClassNameElement($className, $this->helper->getShortClassName($baseClassName)));
        $model->addUse(new UseElement(ltrim($baseClassName, '\\')));
        $model->setTableName($tableName ?: $this->helper->getDefaultTableName($className));

        if ($model->getTableName() !== $this->helper->getDefaultTableName($className)) {
            $property = new PropertyElement('table', 'protected', $model->getTableName());
            $property->setDocBlock(new DocBlockElement('The table associated with the Element.', '', '@var string'));
            $model->addProperty($property);
        }
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 10;
    }
}
