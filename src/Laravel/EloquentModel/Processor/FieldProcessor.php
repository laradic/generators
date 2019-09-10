<?php

namespace Laradic\Generators\Laravel\EloquentModel\Processor;

use Illuminate\Database\DatabaseManager;
use Laradic\Generators\Laravel\EloquentModel\Config;
use Laradic\Generators\Core\Elements\PropertyElement;
use Laradic\Generators\Core\Elements\DocBlockElement;
use Laradic\Generators\Laravel\EloquentModel\TypeRegistry;
use Laradic\Generators\Laravel\EloquentModel\Model\EloquentElement;

/**
 * Class FieldProcessor
 *
 * @package Krlove\EloquentModelGenerator\Processor
 */
class FieldProcessor implements ProcessorInterface
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
     *
     * @param DatabaseManager $databaseManager
     * @param TypeRegistry    $typeRegistry
     */
    public function __construct(DatabaseManager $databaseManager, TypeRegistry $typeRegistry)
    {
        $this->databaseManager = $databaseManager;
        $this->typeRegistry    = $typeRegistry;
    }

    /**
     * @inheritdoc
     */
    public function process(EloquentElement $model, Config $config)
    {
        $schemaManager = $this->databaseManager->connection($config->get('connection'))->getDoctrineSchemaManager();
        $prefix        = $this->databaseManager->connection($config->get('connection'))->getTablePrefix();

        $tableDetails       = $schemaManager->listTableDetails($prefix . $model->getTableName());
        $primaryColumnNames = $tableDetails->getPrimaryKey() ? $tableDetails->getPrimaryKey()->getColumns() : [];

        $columnNames = [];
        foreach ($tableDetails->getColumns() as $column) {
            $name = $column->getName();
            $type = $this->typeRegistry->resolveType($column->getType()->getName());
            $model->getDocBlock()->addContent("@property {$type} \${$name}");

            if ( ! in_array($column->getName(), $primaryColumnNames, true)) {
                $columnNames[] = $column->getName();
            }
        }

        $fillableProperty = new PropertyElement('fillable');
        $fillableProperty->setAccess('protected')
            ->setValue($columnNames)
            ->setDocBlock(new DocBlockElement('@var array'));
        $model->addProperty($fillableProperty);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 5;
    }
}
