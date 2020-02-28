<?php

namespace Laradic\Generators\Doc\Collection;

use Barryvdh\Reflection\DocBlock\Tag\MethodTag;

class MethodTagCollection extends BaseTagCollection
{
    /** @var \Barryvdh\Reflection\DocBlock\Tag\MethodTag[] */
    protected $items = [];

    /**
     * @param $value
     *
     * @return $this|\Barryvdh\Reflection\DocBlock\Tag\MethodTag[]
     */
    public function whereMethodName($value)
    {
        return $this->filter(function (MethodTag $item) use ($value) {
            return $item->getMethodName() === $value;
        });
    }

    /**
     * @param $value
     *
     * @return $this|\Barryvdh\Reflection\DocBlock\Tag\MethodTag[]
     */
    public function whereStatic(bool $value)
    {
        return $this->filter(function (MethodTag $item) use ($value) {
            return $item->isStatic() === $value;
        });
    }

    /**
     * @param $value
     *
     * @return $this|\Barryvdh\Reflection\DocBlock\Tag\MethodTag[]
     */
    public function whereType($value)
    {
        return $this->filter(function (MethodTag $item) use ($value) {
            return $item->getType() === $value;
        });
    }

    /**
     * @param $value
     *
     * @return $this|\Barryvdh\Reflection\DocBlock\Tag\MethodTag[]
     */
    public function whereHasType($value)
    {
        return $this->filter(function (MethodTag $item) use ($value) {
            return in_array($value, $item->getTypes(), true);
        });
    }

    /**
     * @param $value
     *
     * @return $this|\Barryvdh\Reflection\DocBlock\Tag\MethodTag[]
     */
    public function whereNotHasType($value)
    {
        return $this->filter(function (MethodTag $item) use ($value) {
            return ! in_array($value, $item->getTypes(), true);
        });
    }
}
