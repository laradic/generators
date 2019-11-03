<?php
/** @noinspection PhpMissingParentConstructorInspection */
/** @noinspection MagicMethodsValidityInspection */

namespace Laradic\Generators\DocBlock;

use ReflectionClass;
use ReflectionMethod;
use Illuminate\Support\Str;
use Barryvdh\Reflection\DocBlock;

class MethodDoc extends ReflectionMethod
{

    /** @var \Barryvdh\Reflection\DocBlock */
    protected $docBlock;

    public function __construct(string $className, string $methodName)
    {
        parent::__construct($className,$methodName);
//        $this->class      = new ReflectionClass($className);
//        $this->reflection = new ReflectionMethod($className, $methodName);
        $this->docBlock   = new DocBlock($this, new DocBlock\Context($this->getNamespaceName()));
    }

    protected $ensureTags = [
        'lines'      => [
            // $tag_line => $tag
        ],
        'params'    => [
            // $methodName => $methodTag
        ]
    ];

    public function ensure(string $name, string $content)
    {
        $tagLine                                 = "@{$name} {$content}";
        $tag                                     = DocBlock\Tag::createInstance($tagLine);
        $this->ensureTags[ 'lines' ][ $tagLine ] = $tag;
        return $tag;
    }


    public function getContent()
    {
        return file_get_contents($this->getFileName());
    }

    public function getFileInfo()
    {
        return new \SplFileInfo($this->getFileName());
    }

    /**
     * @return \Laradic\Generators\DocBlock\TagCollection
     */
    protected function getTags()
    {
        return TagCollection::make($this->docBlock->getTags());
    }


    protected function getTagsByName($name)
    {
        return TagCollection::make($this->docBlock->getTagsByName($name));
    }

    protected function hasTag(string $name, string $content)
    {
        return $this->getTagsByName($name)
            ->map->getContent()
            ->filter(function ($tagContent) use ($content) {
                return Str::contains($tagContent, $content);
            })
            ->isNotEmpty();
    }

    public function getDocBlock()
    {
        return $this->docBlock;
    }

    public function clearTagsByName($name)
    {
        foreach ($this->docBlock->getTagsByName($name) as $tag) {
            $this->docBlock->deleteTag($tag);
        }
        return $this;
    }
}
