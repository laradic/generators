<?php

namespace Laradic\Generators\Core\Interfaces;

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
