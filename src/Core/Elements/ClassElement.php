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
            $content = [];
            foreach ($this->properties->virtual() as $property) {
                $content[] = $property->toLines();
            }
            foreach ($this->methods->virtual() as $method) {
                $content[] = $method->toLines();
            }
            $this->docBlock->addContent($content);
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
            $lines[] = $this->properties->real()->render(4, PHP_EOL, PHP_EOL);
        }
        if ($this->methods->isNotEmpty()) {
            $lines[] = $this->methods->real()->render(4, PHP_EOL, PHP_EOL);
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

    public function addDocblockMethod($method, $type = null)
    {
        if ( ! $method instanceof VirtualMethodElement) {
            $method = new VirtualMethodElement($method, $type);
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

    public function addDocblockProperty($property, $type = null)
    {
        if ( ! $property instanceof VirtualPropertyElement) {
            $property = new VirtualPropertyElement($property, $type);
        }
        $this->properties->put($property->getName(), $property);
        return $property;
    }

    public function setExtends($name, $ifClassAddToUse = true)
    {
        $shortName = $name;
        if (class_exists($name) && $ifClassAddToUse) {
            $this->addUse($name);
            $shortName = (new \ReflectionClass($name))->getShortName();
        }
        $this->getName()->setExtends($shortName);
    }

    public function addImplement($name, $ifClassAddToUse = true)
    {
        $shortName = $name;
        if (class_exists($name) && $ifClassAddToUse) {
            $this->addUse($name);
            $shortName = (new \ReflectionClass($name))->getShortName();
        }
        $this->getName()->addImplements($shortName );
    }

    public function addTrait($name, $ifClassAddToUse = true)
    {
        $shortName = $name;
        if (class_exists($name) && $ifClassAddToUse) {
            $this->addUse($name);
            $shortName = (new \ReflectionClass($name))->getShortName();
        }
        $this->addUseTrait([ $shortName ]);
    }


    public function getName()
    {
        return $this->name;
    }

    public function getUses()
    {
        return $this->uses->keyBy->getUse();
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getTraits()
    {
        return $this->traits->keyBy->getName();
    }

    public function getConstants()
    {
        return $this->constants->keyBy->getName();
    }

    public function getProperties()
    {
        return $this->properties->keyBy->getName();
    }

    public function getMethods()
    {
        return $this->methods->keyBy->getName();
    }


}
