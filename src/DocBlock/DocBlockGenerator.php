<?php

namespace Laradic\Generators\DocBlock;

use Illuminate\Support\Str;

class DocBlockGenerator
{
    /** @var \Laradic\Generators\DocBlock\ClassDoc[] */
    protected $classes = [];
    protected $methods = [];

    protected $classDocClass = ClassDoc::class;

    /**
     * @return \Laradic\Generators\DocBlock\ProcessedClassDoc[]|\Illuminate\Support\Collection
     */
    public function process()
    {
        $classes   = $this->classes;
        $processed = [];
        while ( ! empty($classes)) {
            $class       = array_shift($classes);
            $processed[] = $class->process();
        }
        return collect($processed);
    }

    public function class($class)
    {
        if ( ! $this->hasClass($class)) {
            $this->addNewClass($class);
        }
        return $this->getClass($class);
    }

    public function addNewClass($class)
    {
        return $this->addClass($this->newClass($class));
    }

    public function addClass(ClassDoc $class)
    {
        $name                   = $this->className($class->getName());
        $this->classes[ $name ] = $class;
        return $this->classes[ $name ];
    }

    public function newClass($class)
    {
        return new $this->classDocClass($class);
    }

    public function getClass($class)
    {
        return $this->classes[ $this->className($class) ];
    }

    public function hasClass($class)
    {
        return array_key_exists($this->className($class), $this->classes);
    }

    protected function className($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        $class = Str::ensureLeft($class, '\\');
        return $class;
    }

    public function setClassDocClass($classDocClass)
    {
        $this->classDocClass = $classDocClass;
        return $this;
    }

}
