<?php

namespace Laradic\Generators\Core\Elements;

use Laradic\Generators\Core\Traits\DocBlockTrait;
use Laradic\Generators\Core\Traits\InitsCollections;
use Laradic\Generators\Core\Collections\ElementCollection;
use Laradic\Generators\Core\Collections\NamedElementCollection;
use Laradic\Generators\Core\Collections\MethodElementCollection;
use Laradic\Generators\Core\Collections\PropertyElementCollection;

class ClassElement extends Element
{
    use InitsCollections;
    use DocBlockTrait;

    /** @var \Laradic\Generators\Core\Elements\ClassNameElement */
    protected $name;

    /** @var \Laradic\Generators\Core\Elements\UseElement[]|ElementCollection */
    protected $uses;

    /** @var \Laradic\Generators\Core\Elements\NamespaceElement */
    protected $namespace;

    /** @var \Laradic\Generators\Core\Elements\UseTraitElement[]|ElementCollection */
    protected $traits;

    /** @var \Laradic\Generators\Core\Elements\ConstantElement[]|NamedElementCollection */
    protected $constants;

    /** @var \Laradic\Generators\Core\Elements\PropertyElement[]|\Laradic\Generators\Core\Collections\PropertyElementCollection */
    protected $properties;

    /** @var \Laradic\Generators\Core\Elements\MethodElement[]|\Laradic\Generators\Core\Collections\MethodElementCollection */
    protected $methods;

    protected $collections = [
        'uses'       => ElementCollection::class,
        'traits'     => ElementCollection::class,
        'constants'  => NamedElementCollection::class,
        'properties' => PropertyElementCollection::class,
        'methods'    => MethodElementCollection::class,
    ];

    public function __construct()
    {
        $this->initCollections();
        $this->docBlock = new DocBlockElement();
    }

    /**
     * @return string|string[]
     * @throws \Exception
     */
    public function toLines()
    {
        $lines = [ $this->ln('<?php') ];
        if ($this->namespace !== null) {
            $lines[] = $this->ln($this->getNamespace()->render());
        }
        if ($this->uses->isNotEmpty()) {
            $lines[] = $this->renderArrayLn($this->uses->all());
        }
        if ($this->docBlock !== null) {
            $lines[] = $this->docBlock->render();
        }
        $lines [] = $this->name->render();

        if ($this->traits->isNotEmpty()) {
            $lines[] = $this->traits->render(4, PHP_EOL, PHP_EOL);
        }
        if ($this->constants->isNotEmpty()) {
            $lines[] = $this->constants->render(4, PHP_EOL, PHP_EOL);
        }
        if ($this->properties->isNotEmpty()) {
            $lines[] = $this->properties->render(4, PHP_EOL, PHP_EOL);
        }
        if ($this->methods->isNotEmpty()) {
            $lines[] = $this->methods->render(4, PHP_EOL, PHP_EOL);
        }

        $lines[] = $this->ln('}');
        return $lines;
    }

    public function setName($name, ?string $extends = null, array $implements = [])
    {
        if ( ! $name instanceof ClassNameElement) {
            $name = new ClassNameElement($name, $extends, $implements);
        }
        $this->name = $name;
        return $this;
    }

    public function addUse($use)
    {
        if ( ! $use instanceof UseElement) {
            $use = new UseElement($use);
        }
        $this->uses[] = $use;
        return $this;
    }

    public function setNamespace($namespace)
    {
        if ( ! $namespace instanceof NamespaceElement) {
            $namespace = new NamespaceElement($namespace);
        }
        $this->namespace = $namespace;
        return $this;
    }

    public function addUseTrait($trait)
    {
        if ( ! $trait instanceof UseTraitElement) {
            $trait = new UseTraitElement($trait);
        }
        $this->traits[] = $trait;
        return $trait;
    }

    public function addConstant($constant, $value = null)
    {
        if ( ! $constant instanceof ConstantElement) {
            $constant = new ConstantElement($constant, $value);
        }
        $this->constants->put($constant->getName(), $constant);
        return $constant;
    }

    public function addMethod($method, $access = 'public')
    {
        if ( ! $method instanceof MethodElement) {
            $method = new MethodElement($method, $access);
        }
        $this->methods->put($method->getName(), $method);
        return $method;
    }

    public function addProperty($property, $access = 'public', $default = null)
    {
        if ( ! $property instanceof PropertyElement) {
            $property = new PropertyElement($property, $access, $default);
        }
        $this->properties->put($property->getName(), $property);
        return $property;
    }


    public function getName()
    {
        return $this->name;
    }

    public function getUses()
    {
        return $this->uses;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getTraits()
    {
        return $this->traits;
    }

    public function getConstants()
    {
        return $this->constants;
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function getMethods()
    {
        return $this->methods;
    }


}
