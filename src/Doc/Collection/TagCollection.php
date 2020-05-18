<?php

namespace Laradic\Generators\Doc\Collection;

use Barryvdh\Reflection\DocBlock\Tag;

class TagCollection extends BaseTagCollection
{
    public function getMethodTags()
    {
        return new MethodTagCollection($this->whereName('method'));
    }

    public function getMixinTags()
    {
        return new MixinTagCollection($this->whereName('mixin'));
    }

    public function getParamTags()
    {
        return new ParamTagCollection($this->whereName('param'));
    }

    public function getPropertyTags()
    {
        return new PropertyTagCollection($this->whereName('property'));
    }

    /**
     * @param $where
     * @param $value
     * @return $this|Tag[]
     */
    public function getWhere($where, $value)
    {
        $method = camel_case('get_' . $where);
        return $this->filter(function (Tag $item) use ($method, $value) {
            return $item->{$method}() === $value;
        });
    }
}
