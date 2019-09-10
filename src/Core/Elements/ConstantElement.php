<?php


namespace Laradic\Generators\Core\Elements;


use Laradic\Generators\Core\Traits\ValueTrait;
use Laradic\Generators\Core\Interfaces\NameableInterface;

class ConstantElement extends Element implements NameableInterface
{
    use ValueTrait;

    /** @var string */
    protected $name;

    public function __construct($name, $value = null)
    {
        $this
            ->setName($name)
            ->setValue($value);
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

    public function toLines()
    {
        return sprintf('const %s = %s;', $this->getName(), $this->renderValue());
    }
}
