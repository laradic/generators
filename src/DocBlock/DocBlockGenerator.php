<?php

namespace Laradic\Generators\DocBlock;

use Closure;
use Illuminate\Support\Str;
use Laradic\Generators\DocBlock\Definition\ClassDefinition;
use Laradic\Generators\DocBlock\Definition\DefinitionBuilder;

class DocBlockGenerator
{
    /** @var \Laradic\Generators\DocBlock\Definition\ClassDefinition[] */
    protected $classes = [];
    protected $methods = [];

    protected $classDocClass = ClassDefinition::class;
    /** @var array Closure[] */
    protected $callbacks = [];

    /**
     * @return \Laradic\Generators\DocBlock\ProcessedClassDefinition[]|\Illuminate\Support\Collection
     */
    public function process()
    {
        $classes   = $this->classes;
        $processed = [];
        while ( ! empty($classes)) {
            $class = array_shift($classes);
            $class->collect()->process();
            $processed[] =$class;
//            if (array_key_exists($class->getReflectionName(), $this->callbacks)) {
//                $callback = $this->callbacks[ $class->getReflectionName() ];
//                $result   = $callback($class); // ProcessedClassDoc = add to processed, false = skip, otherwise process self
//                if ($result instanceof ProcessedClassDefinition) {
//                    $processed[] = $result;
//                } elseif ($result !== false) {
//                    $processed[] = $class->process();
//                }
//            } else {
//                $processed[] = $class->process();
//            }
        }
        return collect($processed);
    }

    public function setClassDocClass($classDocClass)
    {
        $this->classDocClass = $classDocClass;
        return $this;
    }

    /**
     * @param          $class
     * @param \Closure $cb is called with parameter of type ClassDoc. If return false: Skip. If return ProcessedClassDoc: add to processed. Return anything else: continue as normal
     * @return $this
     */
    public function onClass($class, Closure $cb)
    {
        $class                     = is_object($class) ? get_class($class) : $class;
        $class                     = Str::ensureLeft($class, '\\');
        $this->callbacks[ $class ] = $cb;
        return $this;
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

    public function addClass(ClassDefinition $class)
    {
        $name                   = $this->className($class->getReflectionName());
        $this->classes[ $name ] = $class;
        return $this->classes[ $name ];
    }

    public function newClass($class)
    {
        return resolve(DefinitionBuilder::class)->class($class);
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

}
