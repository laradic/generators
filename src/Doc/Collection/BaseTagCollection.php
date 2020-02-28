<?php

namespace Laradic\Generators\Doc\Collection;

use Barryvdh\Reflection\DocBlock\Tag;
use Illuminate\Support\Collection;
use Laradic\Generators\Doc\Docblock;

abstract class BaseTagCollection extends Collection
{
    /** @var \Barryvdh\Reflection\DocBlock\Tag[] */
    protected $items = [];

    /**
     * @var \Laradic\Generators\Doc\Docblock
     */
    protected $docblock;

    public function setDocBlock(DocBlock $docblock)
    {
        $this->docblock=$docblock;
        return $this;
    }

    /**
     * @param $value
     *
     * @return $this|\Barryvdh\Reflection\DocBlock\Tag[]
     */
    public function whereContent($value)
    {
        return $this->filter(function(Tag $item) use ($value) {
            return $item->getContent() === $value;
        });
    }

    /**
     * @param $value
     *
     * @return $this|\Barryvdh\Reflection\DocBlock\Tag[]
     */
    public function whereDescription($value)
    {
        return $this->filter(function(Tag $item) use ($value) {
            return $item->getDescription() === $value;
        });
    }

    /**
     * @param $value
     *
     * @return $this|\Barryvdh\Reflection\DocBlock\Tag[]
     */
    public function whereName($value)
    {
        return $this->filter(function(Tag $item) use ($value) {
            return $item->getName() === $value;
        });
    }

    public function delete()
    {
        return $this->deleteFrom($this->docblock);
    }

    public function append()
    {
        return $this->appendTo($this->docblock);
    }

    public function deleteFrom(Docblock $docblock)
    {
        foreach($this->items as $item){
            $docblock->deleteTag($item);
        }
        return $this;
    }

    public function appendTo(Docblock $docblock)
    {
        foreach($this->items as $item){
            $docblock->appendTag($item);
        }
        return $this;
    }
}
