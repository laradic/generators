<?php

namespace Laradic\Generators\Doc\Doc;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Laradic\Generators\Doc\DocBlock;

abstract class BaseDoc implements Doc
{
    use Macroable;

    /** @var string */
    protected $className;

    /** @var \ReflectionClass|\ReflectionMethod|\ReflectionProperty */
    protected $reflection;

    /** @var DocBlock */
    protected $docblock;

    public function getClassName()
    {
        return $this->className;
    }

    public function getReflection()
    {
        return $this->reflection;
    }

    public function getDocblock()
    {
        return $this->docblock;
    }

    public function cleanTag($name)
    {
        $this->getTags()->whereName($name)->deleteFrom($this->docblock);
        return $this;
    }


    public function getTags()
    {
        return $this->docblock->getTags()->setDocBlock($this->docblock);
    }


    protected function resolveType(&$type)
    {
        $type = Collection::wrap($type);
        $type = $type->map(function ($item) {
            if ($item instanceof Doc) {
                $item = Str::ensureLeft($item->getReflection()->getName(),'\\');
            }
            if (is_object($item)) {
                $item = get_class($item);
            }
            if (class_exists($item)) {
                $item = Str::ensureLeft($item, '\\');
            }
            return $item;
        });
        return $type = $type->implode('|');
    }
}
