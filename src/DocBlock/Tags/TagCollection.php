<?php

namespace Laradic\Generators\DocBlock\Tags;

use Illuminate\Support\Str;
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
        $tags = $this->map([ $tags, 'getMatching' ]);
        $tags->each([ $docblock, 'deleteTag' ]);
        return $this;
    }

    public function appendToDocblock(DocBlock $docblock)
    {
        foreach($this->items as $item){
            $docblock->appendTag($item);
        }
        return $this;
    }

    public function getMatching(Tag $matcher)
    {
        return $this->first(function (Tag $tag) use ($matcher) {
            return $tag->getName() == $matcher->getName() && $tag->getContent() === $matcher->getContent();
        });
    }

    /**
     * @param Tag|string $tag
     * @return \Laradic\Generators\DocBlock\Tags\TagTypeCollection
     */
    public function collectTagType($tag)
    {
        $type  = TagUtil::resolveTagType($tag);
        $items = $this->filter(function (Tag $item) use ($type) {
            return $item instanceof $type;
        })->all();
        return TagTypeCollection::makeForType($type, $items);
    }

    /**
     * Get the tag type names currently in the collection
     *
     * @return $this
     */
    public function getTagTypes()
    {
        return $this->map(function(Tag $tag){
            return TagUtil::resolveTagType($tag);
        })->unique();
    }

    public function mapToTagTypeCollections()
    {
        return $this->getTagTypes()->map(function ($type) {
            return $this->collectTagType($type);
        });
    }

    protected function callCollectTagType(string $tag)
    {
        return $this->collectTagType(Str::singular($tag));
    }

    public function methods()
    {
        return $this->callCollectTagType(__METHOD__);
    }

    public function params()
    {
        return $this->callCollectTagType(__METHOD__);
    }

    public function properties()
    {
        return $this->callCollectTagType(__METHOD__);
    }

    public function vars()
    {
        return $this->callCollectTagType(__METHOD__);
    }

    public function covers()
    {
        return $this->callCollectTagType(__METHOD__);
    }

    public function deprecates()
    {
        return $this->callCollectTagType(__METHOD__);
    }

    public function examples()
    {
        return $this->callCollectTagType(__METHOD__);
    }

    public function links()
    {
        return $this->callCollectTagType(__METHOD__);
    }

    public function returns()
    {
        return $this->callCollectTagType(__METHOD__);
    }

    public function sees()
    {
        return $this->callCollectTagType(__METHOD__);
    }

    public function since()
    {
        return $this->callCollectTagType(__METHOD__);
    }

    public function sources()
    {
        return $this->callCollectTagType(__METHOD__);
    }

    public function throws()
    {
        return $this->callCollectTagType(__METHOD__);
    }

    public function uses()
    {
        return $this->callCollectTagType(__METHOD__);
    }
}
