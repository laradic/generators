<?php

namespace Laradic\Generators\Core\Elements;

class UseElement extends Element
{
    /** @var string */
    protected $use;

    public function __construct($use)
    {
        $this->setUse($use);
    }

    public function getUse()
    {
        return $this->use;
    }

    public function setUse($use)
    {
        $this->use = (string)$use;
        return $this;
    }

    public function toLines()
    {
        return ['use ' . $this->use . ';'];
    }
}
