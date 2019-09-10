<?php

namespace Laradic\Generators\DocBlock;

use ReflectionClass;
use Illuminate\Support\Str;
use Barryvdh\Reflection\DocBlock;
use Barryvdh\Reflection\DocBlock\Serializer as DocBlockSerializer;


class ClassDoc
{
    /** @var \ReflectionClass */
    protected $reflection;
    /** @var \phpDocumentor\Reflection\DocBlock */
    protected $docBlock;

    public function __construct(string $className)
    {
        $this->reflection = new ReflectionClass($className);
        $this->docBlock   = new DocBlock($this->reflection, new DocBlock\Context($this->reflection->getNamespaceName()));
    }

    public function ensureTag(string $name, string $content)
    {
        if ( ! $this->docBlock->getText()) {
            $this->docBlock->setText($this->reflection->getName());
        }
        if ( ! $this->hasTag($name, $content)) {
            $tag = DocBlock\Tag::createInstance("@{$name} {$content}");
            $this->docBlock->appendTag($tag);
        }
        return $this;
    }

    public function hasTag(string $name, string $content)
    {
        return $this->getTagsByName($name)
            ->map->getContent()
            ->filter(function ($tagContent) use ($content) {
                return Str::contains($tagContent, $content);
            })
            ->isNotEmpty();
    }

    public function getTags()
    {
        return TagCollection::make($this->docBlock->getTags());
    }

    public function getTagsByName($name)
    {
        return TagCollection::make($this->docBlock->getTagsByName($name));
    }

    public function process()
    {
        $serializer = new DocBlockSerializer();
        $serializer->getDocComment($this->docBlock);

        $docComment         = $serializer->getDocComment($this->docBlock);
        $originalDocComment = $this->reflection->getDocComment();
        $classname          = $this->reflection->getShortName();
        $filename           = $this->reflection->getFileName();
        $contents           = file_get_contents($filename);
        /** @noinspection ClassMemberExistenceCheckInspection */
        $type = method_exists($this->reflection, 'isInterface') && $this->reflection->isInterface() ? 'interface' : 'class';

        if ($originalDocComment) {
            $contents = str_replace($originalDocComment, $docComment, $contents);
        } else {
            $needle  = "{$type} {$classname}";
            $replace = "{$docComment}\n{$type} {$classname}";
            $pos     = strpos($contents, $needle);
            if ($pos !== false) {
                $contents = substr_replace($contents, $replace, $pos, strlen($needle));
            }
        }
        return $contents;
    }

    public function getReflection()
    {
        return $this->reflection;
    }

    public function getDocBlock()
    {
        return $this->docBlock;
    }
}
