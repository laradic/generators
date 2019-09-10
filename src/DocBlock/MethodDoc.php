<?php
/** @noinspection PhpMissingParentConstructorInspection */
/** @noinspection MagicMethodsValidityInspection */

namespace Laradic\Generators\DocBlock;

use ReflectionClass;
use ReflectionMethod;
use Barryvdh\Reflection\DocBlock;

class MethodDoc extends ClassDoc
{
    /** @var \ReflectionMethod */
    protected $reflection;
    /** @var \ReflectionClass */
    protected $class;

    public function __construct(string $className, string $methodName)
    {
        $this->class      = new ReflectionClass($className);
        $this->reflection = new ReflectionMethod($className, $methodName);
        $this->docBlock   = new DocBlock($this->reflection, new DocBlock\Context($this->reflection->getNamespaceName()));
    }


}
