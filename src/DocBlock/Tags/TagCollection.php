<?php

namespace Laradic\Generators\DocBlock\Tags;

use Barryvdh\Reflection\DocBlock;
use Illuminate\Support\Collection;
use Barryvdh\Reflection\DocBlock\Tag;

/**
 * @method \Barryvdh\Reflection\DocBlock\Tag[] all()
 */
class TagCollection extends Collection
{
    /** @var \Barryvdh\Reflection\DocBlock\Tag[] */
    protected $items = [];

    public function deleteFromDocblock(DocBlock $docblock)
    {
        $tags = static::make($docblock->getTags());
        $tags = $this->map([$tags,'getMatching']);
        $tags->each([ $docblock, 'deleteTag' ]);
        return  $this;
    }

    public function appendToDocblock(DocBlock $docblock)
    {
//        $this->each->setDocBlock();
        return $this->each([ $docblock, 'appendTag' ]);
    }

    public function getMatching(Tag $matcher)
    {
        return $this->first(function(Tag $tag) use ($matcher){
            return $tag->getName() == $matcher->getName() && $tag->getContent() === $matcher->getContent();
        });
    }

    /**
     * @param Tag|string $tag
     * @return \Laradic\Generators\DocBlock\Tags\TagTypeCollection
     */
    public function type($tag)
    {
        $type = TagUtil::resolveTagType($tag);
        $items = $this->filter(function (Tag $item) use ($type) {
            return $item instanceof $type;
        })->all();
        return TagTypeCollection::makeForTag($type, $items);
    }

    public function methods()
    {
        return $this->type('method');
    }

    public function params()
    {
        return $this->type('param');
    }

    public function properties()
    {
        return $this->type('property');
    }

    public function vars()
    {
        return $this->type('var');
    }

    public function covers()
    {
        return $this->type('covers');
    }

    public function deprecates()
    {
        return $this->type('deprecated');
    }

    public function examples()
    {
        return $this->type('example');
    }

    public function links()
    {
        return $this->type('link');
    }

    public function returns()
    {
        return $this->type('return');
    }

    public function sees()
    {
        return $this->type('see');
    }

    public function since()
    {
        return $this->type('since');
    }

    public function sources()
    {
        return $this->type('source');
    }

    public function throws()
    {
        return $this->type('throws');
    }

    public function uses()
    {
        return $this->type('use');
    }
}
