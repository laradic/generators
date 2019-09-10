<?php

namespace Laradic\Generators\Laravel\EloquentModel\Model;

use Laradic\Generators\Core\Elements\ClassElement;

/**
 * Class EloquentElement
 * @package Krlove\EloquentModelGenerator\Model
 */
class EloquentElement extends ClassElement
{
    /**
     * @var string
     */
    protected $tableName;

    /**
     * @param string $tableName
     *
     * @return $this
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }
}
