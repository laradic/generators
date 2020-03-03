<?php

namespace Laradic\Generators\Doc\Doc;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Laradic\Generators\Doc\DocBlock;
use Illuminate\Support\Traits\Macroable;

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
                $item = Str::ensureLeft($item->getReflection()->getName(), '\\');
            }
            if (is_object($item)) {
                $item = get_class($item);
            }
            $isArray = Str::endsWith($item, '[]');
            if ($isArray) {
                $item = Str::removeRight($item, '[]');
            }
            if (class_exists($item)) {
                $item = Str::ensureLeft($item, '\\');
            }
            if ($isArray) {
                $item = Str::ensureRight($item, '[]');
            }
            return $item;
        });
        return $type = $type->implode('|');
    }
}
