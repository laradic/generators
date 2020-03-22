<?php


namespace Laradic\Generators\Core\Collections;


use Laradic\Generators\Core\Elements\VirtualMethodElement;

class MethodElementCollection extends NamedElementCollection
{
    /** @var \Laradic\Generators\Core\Elements\MethodElement[]|\Laradic\Generators\Core\Elements\VirtualMethodElement[] */
    protected $items;

    public function virtual()
    {
        return $this->filter(function ($item) {
            return $item instanceof VirtualMethodElement;
        });
    }

    public function real()
    {
        return $this->filter(function ($item) {
            return ! $item instanceof VirtualMethodElement;
        });
    }

}
