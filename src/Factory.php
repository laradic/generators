<?php

namespace Laradic\Generators;

use Laradic\Generators\Base\Model\NamespaceModel;

class Factory
{
    /** @var \Laradic\Generators\Base\Model\ClassModel */
    protected $model;

    public function setNamespace($namespace)
    {
        if ( ! $namespace instanceof NamespaceModel) {
            $namespace = new NamespaceModel($namespace);
        }
    }

    public function addImplements()
    {

    }
}
