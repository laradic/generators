<?php /** @noinspection OverridingDeprecatedMethodInspection */

/** @noinspection CallableParameterUseCaseInTypeContextInspection */

namespace Laradic\Generators\DocBlock\Tags;

use Barryvdh\Reflection\DocBlock;
use Illuminate\Support\Collection;
use Barryvdh\Reflection\DocBlock\Tag;

class TagTypeCollection extends Collection
{
    protected $tagType = Tag::class;

    public function __construct($items = [])
    {
        parent::__construct();
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    public static function makeForType($tagType, $items = [])
    {
        return with(new static($items))->setTagType($tagType);
    }

    public function deleteFromDocblock(DocBlock $docblock)
    {
        return $this->each([ $docblock, 'deleteTag' ]);
    }

    public function appendToDocblock(DocBlock $docblock)
    {
        return $this->each([ $docblock, 'appendTag' ]);
    }

    public function hasNamed($name)
    {
        if ($this->isNamed()) {
            return $this->has($name);
        }
        return false;
    }

    public function isNamed()
    {
        return $this->named;
    }

    protected $named = false;

    protected function resolveName(Tag $tag)
    {
        $this->named = true;
        if (method_exists($tag, 'getMethodName')) {
            return $tag->getMethodName();
        }
        if (method_exists($tag, 'getVariableName')) {
            return $tag->getVariableName();
        }
        $this->named = false;
        return null;
    }

    public function getTagType()
    {
        return $this->tagType;
    }

    public function setTagType($tagType)
    {
        $this->tagType = TagUtil::resolveTagType($tagType);
        return $this;
    }

    /**
     * @param \Barryvdh\Reflection\DocBlock\Tag $tag
     * @return $this
     */
    public function add($tag)
    {
        if ($name = $this->resolveName($tag)) {
            parent::put($name, $tag);
        } else {
            parent::push($tag);
        }
        return $this;
    }

    /**
     * @param string|\Barryvdh\Reflection\DocBlock\Tag $tagOrVariableName
     * @return bool
     */
    public function has($tagOrVariableName)
    {
        if ($tagOrVariableName instanceof $this->tagType) {
            if ($name = $this->resolveName($tagOrVariableName)) {
                $tagOrVariableName = $name;
            }
        }
        return parent::has($tagOrVariableName);
    }


}