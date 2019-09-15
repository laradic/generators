<?php

namespace Laradic\Generators\DocBlock;

use Barryvdh\Reflection\DocBlock\Tag\MethodTag;

/**
 * @method \Barryvdh\Reflection\DocBlock\Tag\MethodTag get($methodName)
 * @method \Barryvdh\Reflection\DocBlock\Tag\MethodTag[] all()
 */
class MethodTagCollection extends TagCollection
{
    /** @var \Barryvdh\Reflection\DocBlock\Tag\MethodTag[] */
    protected $items = [];

    /**
     * MethodTagCollection constructor.
     *
     * @param \Barryvdh\Reflection\DocBlock\Tag\MethodTag[] $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct();
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * @param MethodTag $tag
     * @return $this
     */
    public function add($tag)
    {
        $this->put($tag->getMethodName(), $tag);
        return $this;
    }

    /**
     * @param string|MethodTag $tagOrMethodName
     * @return bool
     */
    public function has($tagOrMethodName)
    {
        if($tagOrMethodName instanceof MethodTag){
            $tagOrMethodName = $tagOrMethodName->getMethodName();
        }
        return parent::has($tagOrMethodName);
    }
}
