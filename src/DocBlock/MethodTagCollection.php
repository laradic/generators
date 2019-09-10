<?php

namespace Laradic\Generators\DocBlock;

use Barryvdh\Reflection\DocBlock\Tag\MethodTag;

/**
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
}
