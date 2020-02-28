<?php

namespace Laradic\Generators\Doc\Collection;

use Barryvdh\Reflection\DocBlock\Tag\ParamTag;
use Illuminate\Support\Str;

class ParamTagCollection extends BaseTagCollection
{
    /** @var \Barryvdh\Reflection\DocBlock\Tag\ParamTag[] */
    protected $items = [];

    /**
     * @param $value
     *
     * @return $this|\Barryvdh\Reflection\DocBlock\Tag\ParamTag[]
     */
    public function whereVariableName($value)
    {
        return $this->filter(function (ParamTag $item) use ($value) {
            return $item->getVariableName() === Str::ensureLeft($value, '$');
        });
    }

    /**
     * @param $value
     *
     * @return $this|\Barryvdh\Reflection\DocBlock\Tag\ParamTag[]
     */
    public function whereVariadic(bool $value)
    {
        return $this->filter(function (ParamTag $item) use ($value) {
            return $item->isVariadic() === $value;
        });
    }

    /**
     * @param $value
     *
     * @return $this|\Barryvdh\Reflection\DocBlock\Tag\ParamTag[]
     */
    public function whereType($value)
    {
        return $this->filter(function (ParamTag $item) use ($value) {
            return $item->getType() === $value;
        });
    }

    /**
     * @param $value
     *
     * @return $this|\Barryvdh\Reflection\DocBlock\Tag\ParamTag[]
     */
    public function whereHasType($value)
    {
        return $this->filter(function (ParamTag $item) use ($value) {
            return in_array($value, $item->getTypes(), true);
        });
    }

    /**
     * @param $value
     *
     * @return $this|\Barryvdh\Reflection\DocBlock\Tag\ParamTag[]
     */
    public function whereNotHasType($value)
    {
        return $this->filter(function (ParamTag $item) use ($value) {
            return ! in_array($value, $item->getTypes(), true);
        });
    }
}
