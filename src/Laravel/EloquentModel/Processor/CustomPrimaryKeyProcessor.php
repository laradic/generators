<?php

namespace Laradic\Generators\Laravel\EloquentModel\Processor;

use Illuminate\Database\DatabaseManager;
use Laradic\Generators\Core\Elements\DocBlockElement;
use Laradic\Generators\Core\Elements\PropertyElement;
use Laradic\Generators\Laravel\EloquentModel\Config;
use Laradic\Generators\Laravel\EloquentModel\TypeRegistry;
use Laradic\Generators\Laravel\EloquentModel\Model\EloquentElement;

/**
 * Class CustomPrimaryKeyProcessor
 * @package Krlove\EloquentModelGenerator\Processor
 */
class CustomPrimaryKeyProcessor implements ProcessorInterface
{
    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * @var TypeRegistry
     */
    protected $typeRegistry;

    /**
     * FieldProcessor constructor.
     * @param DatabaseManager $databaseManager
     * @param TypeRegistry $typeRegistry
     */
    public function __construct(DatabaseManager $databaseManager, TypeRegistry $typeRegistry)
    {
        $this->databaseManager = $databaseManager;
        $this->typeRegistry = $typeRegistry;
    }

    /**
     * @inheritdoc
     */
    public function process(EloquentElement $model, Config $config)
    {
        $schemaManager = $this->databaseManager->connection($config->get('connection'))->getDoctrineSchemaManager();
        $prefix        = $this->databaseManager->connection($config->get('connection'))->getTablePrefix();

        $tableDetails = $schemaManager->listTableDetails($prefix . $model->getTableName());
        $primaryKey = $tableDetails->getPrimaryKey();
        if ($primaryKey === null) {
            return;
        }

        $columns = $primaryKey->getColumns();
        if (count($columns) !== 1) {
            return;
        }

        $column = $tableDetails->getColumn($columns[0]);
        if ($column->getName() !== 'id') {
            $primaryKeyProperty = new PropertyElement('primaryKey', 'protected', $column->getName());
            $primaryKeyProperty->setDocBlock(
                new DocBlockElement('The primary key for the Element.', '', '@var string')
            );
            $model->addProperty($primaryKeyProperty);
        }
        if ($column->getType()->getName() !== 'integer') {
            $keyTypeProperty = new PropertyElement(
                'keyType',
                'protected',
                $this->typeRegistry->resolveType($column->getType()->getName())
            );
            $keyTypeProperty->setDocBlock(
                new DocBlockElement('The "type" of the auto-incrementing ID.', '', '@var string')
            );
            $model->addProperty($keyTypeProperty);
        }
        if ($column->getAutoincrement() !== true) {
            $autoincrementProperty = new PropertyElement('incrementing', 'public', false);
            $autoincrementProperty->setDocBlock(
                new DocBlockElement('Indicates if the IDs are auto-incrementing.', '', '@var bool')
            );
            $model->addProperty($autoincrementProperty);
        }
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 6;
    }
}
