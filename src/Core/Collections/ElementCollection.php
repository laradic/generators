<?php


namespace Laradic\Generators\Core\Collections;


use Illuminate\Support\Collection;

class ElementCollection extends Collection
{
    public function render($ident = 0, $prefix = '', $suffix = '')
    {
        $result = '';
        foreach ($this->items as $item) {
            $result .= $prefix . $item->render($ident) . $suffix;
        }
        return $result;
    }

}
