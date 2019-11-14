<?php

namespace Laradic\Generators\DocBlock\Definition;

use Reflector;
use Illuminate\Support\Str;
use Barryvdh\Reflection\DocBlock;
use Illuminate\Support\Collection;
use Barryvdh\Reflection\DocBlock\Tag;
use Barryvdh\Reflection\DocBlock\Location;
use Laradic\Generators\DocBlock\Tags\TagUtil;
use Laradic\Generators\DocBlock\Tags\TagCollection;
use Barryvdh\Reflection\DocBlock\Serializer as DocBlockSerializer;

class Definition
{
    /** @var string */
    protected $type;
    /** @var \Reflector|\ReflectionProperty|\ReflectionMethod|\ReflectionClass */
    protected $reflection;
    /** @var \Barryvdh\Reflection\DocBlock */
    protected $docBlock;
    /** @var \Laradic\Generators\DocBlock\Tags\TagCollection */
    protected $ensure;
    /** @var \Laradic\Generators\DocBlock\Tags\TagCollection */
    protected $clean;
    protected $processed = false;
    protected $docComment;
    /** @var Location */
    protected $location;

    public function __construct(string $type, Reflector $reflection, DocBlock $docBlock)
    {
        $this->type       = $type;
        $this->reflection = $reflection;
        $this->docBlock   = $docBlock;
        $this->ensure     = TagCollection::make();
        $this->clean      = TagCollection::make();
    }

    public function process($force = false)
    {
        if ( ! $this->processed && ! $force) {
            $docBlock = new DocBlock($this->reflection, $this->docBlock->getContext(), $this->location);
            //$docBlock =$this->docBlock;
//            $this->clean->each->setDocBlock($docBlock);
            $this->clean->deleteFromDocblock($docBlock);
            $tags = $this->ensure->filter(function (Tag $tag) {
                $typeTags = $this->ensure->type($tag);
                TagUtil::resolveTagInnerName($tag);
                return true;
            });
            $missing = $tags->filter(function (Tag $tag) {
                return false === $this->hasTag($tag->getName(), $tag->getContent());
            });
            $missing->appendToDocblock($docBlock);
            $serializer = new DocBlockSerializer();
            $serializer->getDocComment($docBlock);
            $this->docComment = $serializer->getDocComment($docBlock);
            $this->processed  = true;
        }
        return $this->docComment;
    }

    public function isProcessed()
    {
        return $this->processed;
    }

    public function getDocComment()
    {
        $this->process();
        return $this->docComment;
    }

    /**
     * Ensures the defined tag is set in the docblock and matches this tag content and returns the resolved tag handler instance
     *
     * @param string $tag_line
     * @param string $content
     * @param bool   $force
     * @return \Barryvdh\Reflection\DocBlock\Tag|mixed
     */
    public function ensureTag($tag_line, $content = '', bool $force = false)
    {
        $this->resolveType($content); // provide BC
        $tag_line = TagUtil::resolveTagLine($tag_line);
        $tag      = Tag::createInstance($tag_line, null, new Location())->setContent($content);
        $this->ensure->add($tag);
        if ($force) {
            $innerName = TagUtil::resolveTagInnerName($tag);
            $this->cleanTag($tag, $innerName);
        }
        return $tag;
    }

    /**
     * Does the same as ensureTag but returns the definition instance to allow fluent chaining
     *
     * @param        $tag_line
     * @param string $content
     * @param bool   $force
     * @return $this
     *@see \Laradic\Generators\DocBlock\Definition\Definition::ensureTag()
     */
    public function ensure($tag_line, $content = '', bool $force = false){
        $this->ensureTag($tag_line,$content,$force);
        return $this;
    }

    /**
     * @param      $tag_line
     * @param null $innerName method name | property name | var name | etc
     * @return $this
     */
    public function cleanTag($tag_line, $innerName = null)
    {
        $tagName = TagUtil::resolveTagName($tag_line);
        foreach ($this->docBlock->getTagsByName($tagName) as $tag) {
            if ($innerName !== null) {
                if ($innerName === TagUtil::resolveTagInnerName($tag)) {
                    $this->clean->add($tag);
                }
                continue;
            }
            $this->clean->add($tag);
        }
        return $this;
    }

    public function cleanAllTags()
    {
        foreach ($this->docBlock->getTags() as $tag) {
            $this->clean->add($tag);
        }
        return $this;
    }

    /**
     * @return \Laradic\Generators\DocBlock\Tags\TagCollection
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

    /**
     * @param string $content
     * @param bool   $force
     * @return Tag\MethodTag
     */
    public function ensureMethodTag(string $content = '', bool $force = false)
    {
        return $this->ensureTag('method', $content, $force);
    }

    /** @return Tag\PropertyTag */
    public function ensurePropertyTag(string $content = '', bool $force = false)
    {
        return $this->ensureTag('property', $content, $force);
    }

    /** @return Tag\VarTag */
    public function ensureVarTag(string $content = '', bool $force = false)
    {
        return $this->ensureTag('var', $content, $force);
    }

    /** @return Tag\ReturnTag */
    public function ensureReturnTag(string $content = '', bool $force = false)
    {
        return $this->ensureTag('return', $content, $force);
    }

    /** @return Tag\ParamTag */
    public function ensureParamTag(string $content = '', bool $force = false)
    {
        return $this->ensureTag('param', $content, $force);
    }

    public function ensureMethod(string $methodName, $type = '', string $arguments = null, string $description = null)
    {
        $this->resolveType($type);
        $tag = $this->ensureMethodTag();
        $this->callArgs($tag, compact('methodName', 'type', 'arguments', 'description'));
        return $this;
    }

    protected function resolveType(&$type)
    {
        $type = Collection::wrap($type);
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $type = $type->map(function ($item) {
            if ($item instanceof ClassDefinition) {
                $item= $item->getReflectionName(true);
            }
            if (is_object($item)) {
                $item=get_class($item);
            }
            if(class_exists($item)){
                $item= Str::ensureLeft($item,'\\');
            }
            return $item;
        });
        return $type = $type->implode('|');
    }

    protected function callArgs($target, $args = [])
    {
        $_args = collect($args)
            ->evaluate('item != null', 'filter')
            ->mapWithKeys(function ($value, $key) {
                return [ Str::camel('set_' . $key) => $value ];
            });
        foreach ($_args as $method => $value) {
            if (method_exists($target, $method)) {
                $target->{$method}($value);
            }
        }
        return $target;
    }

    public function getEnsureTags()
    {
        return $this->ensure;
    }

    public function getContent()
    {
        return file_get_contents($this->getReflectionFileName());
    }

    public function getFile()
    {
        return new \SplFileInfo($this->getReflectionFileName());
    }

    public function isType($type)
    {
        return $this->type === $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getDocBlock()
    {
        return $this->docBlock;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation(Location $location)
    {
        $this->location = $location;
        return $this;
    }

    public function getReflection()
    {
        return $this->reflection;
    }

    public function getReflectionFileName()
    {
        if (method_exists($this->reflection, 'getFileName')) {
            return $this->reflection->getFileName();
        }
        return $this->reflection->getDeclaringClass()->getFileName();
    }

    public function getReflectionName($prefix = false)
    {
        $name = $this->reflection->getName();
        if ($prefix) {
            $name = Str::ensureLeft($name, '\\');
        }
        return $name;
    }

    public function getReflectionNamespaceName()
    {
        return $this->reflection->getNamespaceName();
    }

    public function getReflectionShortName()
    {
        return $this->reflection->getShortName();
    }

    public function getFilePathname()
    {
        return $this->getFile()->getPathname();
    }

    public function __toString()
    {
        return $this->getReflectionName(true);
    }
}