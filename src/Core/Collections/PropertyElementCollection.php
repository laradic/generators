<?php


namespace Laradic\Generators\Core\Collections;


use Laradic\Generators\Core\Elements\VirtualPropertyElement;

class PropertyElementCollection extends NamedElementCollection
{
    /** @return static|VirtualPropertyElement[] */
    public function virtual()
    {
        return $this->filter(function ($item) {
            return $item instanceof VirtualPropertyElement;
        });
    }

    /** @return static|\Laradic\Generators\Core\Elements\PropertyElement[] */
    public function real()
    {
        return $this->filter(function ($item) {
            return ! $item instanceof VirtualPropertyElement;
        });
    }
}
