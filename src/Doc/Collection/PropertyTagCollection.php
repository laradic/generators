<?php

namespace Laradic\Generators\Doc\Collection;

use Barryvdh\Reflection\DocBlock\Tag\PropertyTag;
use Illuminate\Support\Str;

class PropertyTagCollection extends BaseTagCollection
{
    /** @var \Barryvdh\Reflection\DocBlock\Tag\PropertyTag[] */
    protected $items = [];

    /**
     * @param $value
     *
     * @return $this|\Barryvdh\Reflection\DocBlock\Tag\PropertyTag[]
     */
    public function whereVariableName($value)
    {
        return $this->filter(function (PropertyTag $item) use ($value) {
            return $item->getVariableName() === Str::ensureLeft($value, '$');
        });
    }

    /**
     * @param $value
     *
     * @return $this|\Barryvdh\Reflection\DocBlock\Tag\PropertyTag[]
     */
    public function whereVariadic(bool $value)
    {
        return $this->filter(function (PropertyTag $item) use ($value) {
            return $item->isVariadic() === $value;
        });
    }

    /**
     * @param $value
     *
     * @return $this|\Barryvdh\Reflection\DocBlock\Tag\PropertyTag[]
     */
    public function whereType($value)
    {
        return $this->filter(function (PropertyTag $item) use ($value) {
            return $item->getType() === $value;
        });
    }

    /**
     * @param $value
     *
     * @return $this|\Barryvdh\Reflection\DocBlock\Tag\PropertyTag[]
     */
    public function whereHasType($value)
    {
        return $this->filter(function (PropertyTag $item) use ($value) {
            return in_array($value, $item->getTypes(), true);
        });
    }

    /**
     * @param $value
     *
     * @return $this|\Barryvdh\Reflection\DocBlock\Tag\PropertyTag[]
     */
    public function whereNotHasType($value)
    {
        return $this->filter(function (PropertyTag $item) use ($value) {
            return ! in_array($value, $item->getTypes(), true);
        });
    }
}
