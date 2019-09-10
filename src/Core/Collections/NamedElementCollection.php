<?php


namespace Laradic\Generators\Core\Collections;


use Illuminate\Support\Collection;

class NamedElementCollection extends ElementCollection
{
    /**
     * @param \Laradic\Generators\Core\Interfaces\NameableInterface $item
     * @return $this
     */
    public function add($item)
    {
        $this->put($item->getName(), $item);
        return $this;
    }
}
