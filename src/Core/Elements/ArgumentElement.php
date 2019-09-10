<?php


namespace Laradic\Generators\Core\Elements;


class ArgumentElement extends Element
{
    /** @var string */
    protected $name;

    protected $type;

    protected $default;

    /**
     * ArgumentElement constructor.
     *
     * @param string $name
     * @param        $type
     * @param        $default
     */
    public function __construct(string $name, $type = null, $default = null)
    {
        $this->setName($name)
            ->setType($type)
            ->setDefault($default);
    }


    public function toLines()
    {
        if ($this->type !== null) {
            return $this->type . ' $' . $this->name;
        } else {
            return '$' . $this->name;
        }
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

    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type === null ? null : (string)$type;
        return $this;
    }

    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed $default
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = $default;
        return $this;
    }


}
