<?php

namespace Laradic\Generators\DocBlock;

use Barryvdh\Reflection\DocBlock\Tag\PropertyTag;

/**
 * @method \Barryvdh\Reflection\DocBlock\Tag\PropertyTag get($variableName)
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
        parent::__construct();
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

    /**
     * @param string|\Barryvdh\Reflection\DocBlock\Tag\PropertyTag $tagOrVariableName
     * @return bool
     */
    public function has($tagOrVariableName)
    {
        if($tagOrVariableName instanceof PropertyTag){
            $tagOrVariableName = $tagOrVariableName->getVariableName();
        }
        return parent::has($tagOrVariableName);
    }
}
