<?php

namespace Laradic\Generators\Base;

/**
 * Interface LineableInterface
 * @package Krlove\CodeGenerator
 */
interface LineableInterface
{
    /**
     * @return string|string[]
     */
    public function toLines();
}
