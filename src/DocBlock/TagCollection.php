<?php

namespace Laradic\Generators\DocBlock;

use Illuminate\Support\Collection;
use Barryvdh\Reflection\DocBlock\Tag\MethodTag;
use Barryvdh\Reflection\DocBlock\Tag\PropertyTag;

/**
 * @method \Barryvdh\Reflection\DocBlock\Tag[] all()
 */
class TagCollection extends Collection
{
    /** @var \Barryvdh\Reflection\DocBlock\Tag[] */
    protected $items = [];

    protected $methodTagCollectionClass = MethodTagCollection::class;
    protected $propertyTagCollectionClass = PropertyTagCollection::class;

    public function methods()
    {
        return new $this->methodTagCollectionClass(
            $this
                ->filter(function ($item) {
                    return $item instanceof MethodTag;
                })
                ->all()
        );
    }

    public function properties()
    {
        return new $this->methodTagCollectionClass(
            $this
                ->filter(function ($item) {
                    return $item instanceof PropertyTag;
                })
                ->all()
        );
    }

}
