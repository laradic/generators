<?php

namespace Laradic\Generators\Doc\Collection;

use Illuminate\Support\Str;
use Laradic\Generators\Doc\Tags\MixinTag;

class MixinTagCollection extends BaseTagCollection
{
    /** @var MixinTag[] */
    protected $items = [];

    /**
     * @param $value
     *
     * @return $this|MixinTag[]
     */
    public function whereReference($value)
    {
        return $this->filter(function (MixinTag $item) use ($value) {
            return $item->getReference() === Str::ensureLeft($value, '\\');
        });
    }
}
