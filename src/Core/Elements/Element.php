<?php

namespace Laradic\Generators\Core\Elements;


use Exception;
use Laradic\Generators\Core\Interfaces\LineableInterface;
use Laradic\Generators\Core\Interfaces\RenderableInterface;

/**
 * Class RenderableModel
 *
 * @package Krlove\CodeGenerator
 */
abstract class Element implements RenderableInterface, LineableInterface
{
    /**
     * {@inheritDoc}
     */
    final public function render($indent = 0, $delimiter = PHP_EOL)
    {
        $this->validate();
        $lines = $this->toLines();

        if ( ! is_array($lines)) {
            $lines = [ $lines ];
        }

        if ($indent > 0) {
            array_walk($lines, function (&$item) use ($indent) {
                $item = str_repeat(' ', $indent) . $item;
            });
        }

        return implode($delimiter, $lines);
    }

    /**
     * @return bool
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @param RenderableInterface[] $array
     * @param int                   $indent
     * @param string                $delimiter
     * @return string
     * @throws Exception
     */
    protected function renderArrayLn(array $array, $indent = 0, $delimiter = PHP_EOL)
    {
        return $this->ln($this->renderArray($array, $indent, $delimiter));
    }

    /**
     * @param RenderableInterface[] $array
     * @param int                   $indent
     * @param string                $delimiter
     * @return string
     * @throws Exception
     */
    protected function renderArray(array $array, $indent = 0, $delimiter = PHP_EOL)
    {
        $lines = [];
        foreach ($array as $item) {
            if ( ! $item instanceof RenderableInterface) {
                throw new Exception('Invalid item type');
            }

            $lines[] = $item->render($indent);
        }

        return implode($delimiter, $lines);
    }

    /**
     * @param string $line
     * @return string
     */
    protected function ln($line)
    {
        return $line . PHP_EOL;
    }



    public function __toString()
    {
        return $this->render();
    }
}
