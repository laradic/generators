<?php

namespace Laradic\Generators\DocBlock\Definition;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Barryvdh\Reflection\DocBlock;

class DefinitionBuilder
{
    public function class($class)
    {
        $reflection = new ReflectionClass($this->resolveClass($class));
        $docBlock   = new DocBlock($reflection, new DocBlock\Context($reflection->getNamespaceName()));
        return new ClassDefinition($this,$reflection, $docBlock);
    }

    public function method($class, $name)
    {
        $reflection = new ReflectionMethod($this->resolveClass($class), $name);
        $docBlock   = new DocBlock($reflection, new DocBlock\Context($reflection->getDeclaringClass()->getNamespaceName()));
        return new Definition('method', $reflection, $docBlock);
    }

    public function property($class, $name)
    {
        $reflection = new ReflectionProperty($this->resolveClass($class), $name);
        $docBlock   = new DocBlock($reflection, new DocBlock\Context($reflection->getDeclaringClass()->getNamespaceName()));
        return new Definition('property', $reflection, $docBlock);
    }

    protected function resolveClass($class)
    {
        if ($class instanceof Definition) {
            $class = $class->getReflection()->getName();
        }
        if (is_object($class)) {
            $class = get_class($class);
        }
        return $class;
    }
}