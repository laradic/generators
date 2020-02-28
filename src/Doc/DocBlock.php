<?php

namespace Laradic\Generators\Doc;

use Laradic\Generators\Doc\Collection\MethodTagCollection;
use Laradic\Generators\Doc\Collection\MixinTagCollection;
use Laradic\Generators\Doc\Collection\ParamTagCollection;
use Laradic\Generators\Doc\Collection\PropertyTagCollection;
use Laradic\Generators\Doc\Collection\TagCollection;

class DocBlock extends \Barryvdh\Reflection\DocBlock
{
    /**
     * @param string $name
     *
     * @return \Barryvdh\Reflection\DocBlock\Tag[]|\Laradic\Generators\Doc\Collection\BaseTagCollection
     */
    public function getTagsByName($name)
    {
        return new TagCollection(parent::getTagsByName($name));
    }

    /**
     * @return \Barryvdh\Reflection\DocBlock\Tag[]|\Laradic\Generators\Doc\Collection\BaseTagCollection
     */
    public function getTags()
    {
        return new TagCollection( parent::getTags());
    }

    public function getMethodTags()
    {
        return new MethodTagCollection(parent::getTagsByName('method'));
    }

    public function getMixinTags()
    {
        return new MixinTagCollection(parent::getTagsByName('mixin'));
    }

    public function getParamTags()
    {
        return new ParamTagCollection(parent::getTagsByName('param'));
    }

    public function getPropertyTags()
    {
        return new PropertyTagCollection(parent::getTagsByName('property'));
    }
}
