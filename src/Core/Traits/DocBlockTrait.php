<?php

namespace Laradic\Generators\Core\Traits;

use Laradic\Generators\Core\Elements\DocBlockElement;

trait DocBlockTrait
{
    /**
     * @var DocBlockElement
     */
    protected $docBlock;

    public function getDocBlock()
    {
        return $this->docBlock;
    }

    public function setDocBlock($docBlock)
    {
        if ( ! $docBlock instanceof DocBlockElement) {
            $docBlock = new DocBlockElement($docBlock);
        }

        $this->docBlock = $docBlock;

        return $this;
    }
}
