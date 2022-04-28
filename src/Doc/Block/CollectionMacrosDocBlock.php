<?php

namespace Laradic\Generators\Doc\Block;

use ReflectionParameter;
use ReflectionUnionType;
use ReflectionNamedType;
use Illuminate\Support\Str;
use Barryvdh\Reflection\DocBlock;
use Laradic\Generators\Doc\DocRegistry;
use Barryvdh\Reflection\DocBlock\Tag\SeeTag;
use Barryvdh\Reflection\DocBlock\Tag\MethodTag;

class CollectionMacrosDocBlock
{
    /** @var string */
    protected $collection;

    /** @var string */
    protected $item;
    /** @var array */
    protected $exclude;

    public function __construct(string $collection, string $item = null, array $exclude = [])
    {
        $this->collection = $collection;
        $this->item       = $item;
        $this->exclude    = $exclude;
    }

    public function generate(DocRegistry $registry)
    {
        $collection = Str::ensureLeft($this->collection, '\\');
        $item       = $this->item ? Str::ensureLeft($this->item, '\\') : null;
        $class      = $registry->getClass($collection);
        $ref        = new \ReflectionClass($collection);
        /** @var \ReflectionFunction[] $macros */
        $macros = collect($ref->getStaticProperties()[ 'macros' ])->cast(\ReflectionFunction::class);
        foreach ($macros as $name => $macro) {
            $tag = new MethodTag('method', '');
            $tag->setMethodName($name);

            if ($macro->hasReturnType()) {
                $tag->setType($macro->getReturnType()->getName());
            } else {
                $tag->setType('mixed');
            }
            if ($closureScopeClass = $macro->getClosureScopeClass()) {
                $tags[ 'see_' . $name ] = new SeeTag('see', '\\' . $closureScopeClass->getName());
                if ($comment = $closureScopeClass->getDocComment()) {
                    $docblock = new DocBlock($comment);
                    $tag->setDescription($docblock->getShortDescription());
                    $docParams = $docblock->getTagsByName('param');
                    if ($docReturn = head($docblock->getTagsByName('return'))) {
                        /** @var \Barryvdh\Reflection\DocBlock\Tag\ReturnTag $docReturn */
                        $tag->setType($docReturn->getType());
                    }
                }
            }

            if ($item) {
                if ($tag->getType() === '\\' . \Illuminate\Support\Collection::class) {
                    $tag->setType($collection . '|' . $item . '[]');
                } elseif ($tag->getType() === 'mixed') {
                    $tag->setType($item);
                }
            }
            $arguments = array_map(function (\ReflectionParameter $param) {
                $arg = '';
                if ($this->isCallable($param)) {
                    $arg .= 'callable ';
                } elseif ($type = $param->getType()) {
                    $arg .= $type->getName() . ' ';
                }
                if ($param->isVariadic()) {
                    $arg .= '...';
                } elseif ($param->isPassedByReference()) {
                    $arg .= '&';
                }

                $arg .= '$' . $param->getName();
                if ($param->isDefaultValueAvailable()) {
                    if ($param->isDefaultValueConstant()) {
                        $arg .= ' = ' . $param->getDefaultValueConstantName();
                    } else {
                        $arg .= ' = ' . json_encode($param->getDefaultValue());
                    }
                }

                return $arg;
            }, $macro->getParameters());

            $class->ensureMethod($name, $tag->getType(), implode(', ', $arguments), $tag->getDescription());
            if ($closureScopeClass) {
                $class->ensureSeeTag('\\' . $closureScopeClass->getName());
            }
        }
    }


    protected function isCallable(ReflectionParameter $reflectionParameter): bool
    {
        $reflectionType = $reflectionParameter->getType();

        if (!$reflectionType) return false;

        $types = $reflectionType instanceof ReflectionUnionType
            ? $reflectionType->getTypes()
            : [$reflectionType];

        return in_array('callable', array_map(fn(ReflectionNamedType $t) => $t->getName(), $types));
    }
}
