<?php

namespace Laradic\Generators\Core\Elements;

class NamespaceElement extends Element
{
    /** @var string */
    protected $namespace;

    public function __construct($namespace)
    {
        $this->setNamespace($namespace);
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function setNamespace($namespace)
    {
        $this->namespace = (string)$namespace;
        return $this;
    }

    public function toLines()
    {
        return ['namespace ' . $this->namespace . ';'];
    }
}
