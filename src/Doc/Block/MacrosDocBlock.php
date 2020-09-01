<?php

namespace Laradic\Generators\Doc\Block;

use Barryvdh\Reflection\DocBlock;
use Laradic\Generators\Doc\DocRegistry;
use Barryvdh\Reflection\DocBlock\Tag\SeeTag;
use Barryvdh\Reflection\DocBlock\Tag\MethodTag;

class MacrosDocBlock
{
    /** @var string */
    protected $class;

    /** @var string */
    protected $item;
    /** @var array */
    protected $exclude;

    public function __construct(string $class, array $exclude = [])
    {
        $this->class   = $class;
        $this->exclude = $exclude;
    }

    public function generate(DocRegistry $registry)
    {


        $ref = new \ReflectionClass($this->class);
        /** @var \ReflectionFunction[] $macros */
        $macros = collect($ref->getStaticProperties()[ 'macros' ])->cast(\ReflectionFunction::class);
        foreach ($macros as $name => $macro) {
            $tag = new MethodTag('method', '');
            $tag->setMethodName($name);

            if ($macro->hasReturnType()) {
                $tag->setType((string)$macro->getReturnType());
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
            $arguments = array_map(function (\ReflectionParameter $param) {
                $arg = '';
                if ($param->isCallable()) {
                    $arg .= 'callable ';
                } elseif ($type = $param->getType()) {
                    $arg .= (string)$type . ' ';
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

            $classDoc = $registry->getClass($this->class)
                ->ensureMethod($name, $tag->getType(), implode(', ', $arguments), $tag->getDescription());
            if ($closureScopeClass) {
                $classDoc->ensureSeeTag('\\' . $closureScopeClass->getName());
            }
        }
    }
}
