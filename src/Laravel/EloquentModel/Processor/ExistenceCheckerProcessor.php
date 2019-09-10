<?php

namespace Laradic\Generators\Laravel\EloquentModel\Processor;

use Illuminate\Database\DatabaseManager;
use Laradic\Generators\Laravel\EloquentModel\Config;
use Laradic\Generators\Laravel\EloquentModel\Model\EloquentElement;
use Laradic\Generators\Laravel\EloquentModel\Exception\GeneratorException;

/**
 * Class ExistenceCheckerProcessor
 *
 * @package Krlove\EloquentModelGenerator\Processor
 */
class ExistenceCheckerProcessor implements ProcessorInterface
{
    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * ExistenceCheckerProcessor constructor.
     *
     * @param DatabaseManager $databaseManager
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    /**
     * @inheritdoc
     */
    public function process(EloquentElement $model, Config $config)
    {
        $schemaManager = $this->databaseManager->connection($config->get('connection'))->getDoctrineSchemaManager();
        $prefix        = $this->databaseManager->connection($config->get('connection'))->getTablePrefix();

        if ( ! $schemaManager->tablesExist($prefix . $model->getTableName())) {
            throw new GeneratorException(sprintf('Table %s does not exist', $prefix . $model->getTableName()));
        }
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 8;
    }
}
