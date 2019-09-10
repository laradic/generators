<?php

namespace Laradic\Generators\Core\Elements;

use Laradic\Generators\Core\Traits\FinalModifierTrait;
use Laradic\Generators\Core\Traits\AbstractModifierTrait;

class ClassNameElement extends Element
{
    use FinalModifierTrait;
    use AbstractModifierTrait;

    /** @var string */
    protected $name;

    /** @var string|null */
    protected $extends;

    protected $implements = [];

    public function __construct($name, ?string $extends = null, array $implements = [])
    {
        $this
            ->setName($name)
            ->setExtends($extends)
            ->setImplements($implements);
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = (string)$name;
        return $this;
    }

    public function getExtends()
    {
        return $this->extends;
    }

    public function setExtends($extends)
    {
        $this->extends = $extends === null ? null : (string)$extends;
        return $this;
    }

    public function addImplements(...$implements)
    {
        $this->implements = array_unique(array_merge($this->implements, $implements), SORT_ASC);
        return $this;
    }

    public function getImplements()
    {
        return $this->implements;
    }

    public function setImplements(array $implements)
    {
        $this->implements = $implements;
        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function toLines()
    {

        $lines = [];

        $name = '';
        if ($this->final) {
            $name .= 'final ';
        }
        if ($this->abstract) {
            $name .= 'abstract ';
        }
        $name .= 'class ' . $this->name;

        if ($this->extends !== null && $this->extends) {
            $name .= sprintf(' extends %s', $this->extends);
        }
        if (count($this->implements) > 0) {
            $name .= sprintf(' implements %s', implode(', ', $this->implements));
        }

        $lines[] = $name;
        $lines[] = '{';

        return $lines;
    }
}
