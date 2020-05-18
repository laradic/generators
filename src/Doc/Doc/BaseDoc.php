<?php

namespace Laradic\Generators\Doc\Doc;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Laradic\Generators\Doc\DocBlock;
use Barryvdh\Reflection\DocBlock\Tag;
use Illuminate\Support\Traits\Macroable;

/**
 * @mixin \Laradic\Generators\Doc\DocBlock
 */
abstract class BaseDoc implements Doc
{
    use Macroable {
        __call as callMacro;
    }

    /** @var string */
    protected $className;

    /** @var \ReflectionClass|\ReflectionMethod|\ReflectionProperty */
    protected $reflection;

    /** @var DocBlock */
    protected $docblock;

    protected function makeDocBlock()
    {
        $docblock= new DocBlock($this->reflection);
        $docblock->setDoc($this);
        return $docblock;
    }


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
            if (Str::is( '*::*',$item) || Str::is( '*\\*',$item)) {
                    $item = Str::ensureLeft($item, '\\');
            }
            if ($isArray) {
                $item = Str::ensureRight($item, '[]');
            }
            return $item;
        });
        return $type = $type->implode('|');
    }

    protected function resolveArguments(&$arguments)
    {
        $args = array_map('trim', explode(',', $arguments));
        foreach ($args as &$arg) {
            $argWithType = array_map('trim', explode(' ', $arg));
            if (count($argWithType) === 1) {
                continue;
            }
            $type = head($argWithType);
            if ($type === $arg) {
                continue;
            }
            $this->resolveType($type);
            $argWithType[ 0 ] = $type;
            $arg              = implode(' ', $argWithType);
        }
        return $arguments = implode(', ', $args);
    }

    public function ensureAndReturnSeeTag($classRef, $description = '')
    {
        $this->resolveType($classRef);
        $tagLine = "@see {$classRef} {$description}";
        $this->docblock->getSeeTags()->whereReference($classRef)->deleteFrom($this->docblock);
        $tag = Tag::createInstance($tagLine);
        $this->docblock->appendTag($tag);
        return $tag;
    }

    public function ensureSeeTag($classRef, $description = '')
    {
        $this->ensureAndReturnSeeTag($classRef, $description);
        return $this;
    }

    public function __call($method, $arguments)
    {
        if (method_exists($this->getDocblock(), $method)) {
            return call_user_func_array([ $this->getDocblock(), $method ], $arguments);
        }
        return $this->callMacro($method, $arguments);
    }


}
