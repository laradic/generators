<?php

namespace Laradic\Generators\Doc\Collection;

use Barryvdh\Reflection\DocBlock\Tag\SeeTag;
use Barryvdh\Reflection\DocBlock\Tag\PropertyTag;

class SeeTagCollection extends BaseTagCollection
{
    /** @var \Barryvdh\Reflection\DocBlock\Tag\SeeTag[] */
    protected $items = [];

    /**
     * @param $value
     * @return $this|\Barryvdh\Reflection\DocBlock\Tag\PropertyTag[]
     */
    public function whereReference($value)
    {
        return $this->filter(function (SeeTag $item) use ($value) {
            return $item->getReference() === $value;
        });
    }
}
