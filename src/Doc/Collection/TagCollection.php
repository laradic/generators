<?php

namespace Laradic\Generators\Doc\Collection;

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
}
