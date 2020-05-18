<?php

namespace Laradic\Generators\Doc;

use Barryvdh\Reflection\DocBlock\Context;
use Barryvdh\Reflection\DocBlock\Location;
use Laradic\Generators\Doc\Collection\TagCollection;
use Laradic\Generators\Doc\Collection\SeeTagCollection;
use Laradic\Generators\Doc\Collection\ParamTagCollection;
use Laradic\Generators\Doc\Collection\MixinTagCollection;
use Laradic\Generators\Doc\Collection\MethodTagCollection;
use Laradic\Generators\Doc\Collection\PropertyTagCollection;

/**
 * @mixin \Laradic\Generators\Doc\Doc\Doc
 */
class DocBlock extends \Barryvdh\Reflection\DocBlock
{
    /** @var \Laradic\Generators\Doc\Doc\Doc */
    protected $doc;


    /**
     * @param string $name
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
        return new TagCollection(parent::getTags());
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

    public function getSeeTags()
    {
        return new SeeTagCollection(parent::getTagsByName('see'));
    }

    /**
     * @param $where
     * @param $value
     * @return TagCollection|\Barryvdh\Reflection\DocBlock\Tag[]
     */
    public function getTagsWhere($where, $value)
    {
        return $this->getTags()->getWhere($where, $value);
    }

    public function getDoc()
    {
        return $this->doc;
    }

    public function setDoc($doc)
    {
        $this->doc = $doc;
        return $this;
    }

    public function __call($name, $arguments)
    {
        if(method_exists($this->getDoc(), $name)) {
            return call_user_func_array([ $this->getDoc(), $name ], $arguments);
        }
        throw new \BadMethodCallException("Method [$name] does not exist");
    }


}
