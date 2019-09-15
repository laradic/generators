<?php

namespace Laradic\Generators\DocBlock;

use Illuminate\Support\Str;

class DocBlockGenerator
{
    /** @var \Laradic\Generators\DocBlock\ClassDoc[] */
    protected $classes = [];
    protected $methods = [];

    /**
     * @return \Laradic\Generators\DocBlock\Result[]|\Illuminate\Support\Collection
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
            $this->newClass($class);
        }
        return $this->getClass($class);
    }

    protected function newClass($class)
    {
        $class                   = $this->className($class);
        $this->classes[ $class ] = new ClassDoc($class);
        return $this->classes[ $class ];
    }

    protected function getClass($class)
    {
        return $this->classes[ $this->className($class) ];
    }

    protected function hasClass($class)
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
