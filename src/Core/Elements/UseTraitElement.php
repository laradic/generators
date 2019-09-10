<?php


namespace Laradic\Generators\Core\Elements;


class UseTraitElement extends Element
{

    /** @var string */
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
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
        $this->name = (string) $name;
        return $this;
    }

    public function toLines()
    {
        return sprintf('use %s;', $this->name);
    }
}
