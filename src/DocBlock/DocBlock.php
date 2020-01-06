<?php

namespace Laradic\Generators\DocBlock;

use Barryvdh\Reflection\DocBlock\Context;
use Barryvdh\Reflection\DocBlock\Location;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use Barryvdh\Reflection\DocBlock\Tag;
use Laradic\Generators\DocBlock\Tags\MixinTag;

class DocBlock extends \Barryvdh\Reflection\DocBlock implements Arrayable
{
    public function __construct($docblock, Context $context = null, Location $location = null)
    {
        Tag::registerTagHandler('mixin',MixinTag::class);
        parent::__construct($docblock, $context, $location);
    }

    protected function parseTags($tags)
    {
        parent::parseTags($tags);
        foreach ($this->tags as $key => $value) {
            $name    = '@' . $value->getName();
            $content = $value->getContent();
            if ( ! Str::startsWith($content, $name)) {
                $content = $name . ' ' . $content;
            }
            $value              = Tag::createInstance($content);
            $this->tags[ $key ] = $value;
        }
    }

    public function appendTag(Tag $tag)
    {
        $tagClass = new \ReflectionClass(get_class($tag));
        $this->tags = array_filter($this->tags, function (Tag $t) use ($tag,$tagClass) {
            $tClass = new \ReflectionClass(get_class($t));
            $falses = [
                $tClass->getName() === Tag\MethodTag::class && $tagClass->getName() ===  Tag\MethodTag::class && $t->getMethodName() === $tag->getMethodName(),
                $tClass->getName() === Tag\ParamTag::class && $tagClass->getName() ===  Tag\ParamTag::class && $t->getVariableName() === $tag->getVariableName(),
                $tClass->getName() === Tag\ReturnTag::class && $tagClass->getName() ===  Tag\ReturnTag::class && !$t instanceof Tag\MethodTag && !$tag instanceof Tag\MethodTag,
                $tClass->getName() === Tag\VarTag::class && $tagClass->getName() ===  Tag\VarTag::class,
            ];
            foreach ($falses as $false) {
                if ($false === true) {
                    return false;
                }
            }
            return true;
        });
        return parent::appendTag($tag);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {

        return [
            'short_description' => $this->short_description,
            'long_description' => $this->getLongDescription()->getContents(),
            'tags' => []
        ];
    }
}