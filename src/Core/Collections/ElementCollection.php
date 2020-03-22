<?php


namespace Laradic\Generators\Core\Collections;


use Illuminate\Support\Collection;

class ElementCollection extends Collection
{
    /** @var \Laradic\Generators\Core\Elements\Element[] */
    protected $items = [];
    public function render($ident = 0, $prefix = '', $suffix = '')
    {
        $result = '';
        foreach ($this->items as $item) {
            $result .= $prefix . $item->render($ident) . $suffix;
        }
        return $result;
    }

    public function lines()
    {
        $lines=  [];
        foreach ($this->items as $item) {
            foreach($item->toLines() as $line){
                $lines[] = $line;
            }
        }
        return collect($lines);
    }

}
