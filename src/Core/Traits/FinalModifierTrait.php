<?php

namespace Laradic\Generators\Core\Traits;

trait FinalModifierTrait
{
    /**
     * @var boolean
     */
    protected $final;

    /**
     * @return boolean
     */
    public function isFinal()
    {
        return $this->final;
    }

    /**
     * @param boolean $final
     *
     * @return $this
     */
    public function setFinal($final = true)
    {
        $this->final = (bool)$final;

        return $this;
    }
}
