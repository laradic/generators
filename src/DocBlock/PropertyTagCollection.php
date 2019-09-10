<?php

namespace Laradic\Generators\DocBlock;

/**
 * @method \Barryvdh\Reflection\DocBlock\Tag\PropertyTag[] all()
 */
class PropertyTagCollection extends TagCollection
{
    /** @var \Barryvdh\Reflection\DocBlock\Tag\PropertyTag[] */
    protected $items = [];

    /**
     * MethodTagCollection constructor.
     *
     * @param \Barryvdh\Reflection\DocBlock\Tag\PropertyTag[] $items
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * @param \Barryvdh\Reflection\DocBlock\Tag\PropertyTag $tag
     * @return $this
     */
    public function add($tag)
    {
        $this->put($tag->getVariableName(), $tag);
        return $this;
    }
}
