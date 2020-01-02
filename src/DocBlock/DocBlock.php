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
        $this->tags = array_filter($this->tags, function (Tag $t) use ($tag) {
            $falses = [
                $t instanceof Tag\MethodTag && $tag instanceof Tag\MethodTag && $t->getMethodName() === $tag->getMethodName(),
                $t instanceof Tag\ParamTag && $tag instanceof Tag\ParamTag && $t->getVariableName() === $tag->getVariableName(),
                $t instanceof Tag\ReturnTag && $tag instanceof Tag\ReturnTag && !$t instanceof Tag\MethodTag && !$tag instanceof Tag\MethodTag,
                $t instanceof Tag\VarTag && $tag instanceof Tag\VarTag,
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