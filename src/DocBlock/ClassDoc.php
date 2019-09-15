<?php

namespace Laradic\Generators\DocBlock;

use ReflectionClass;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Barryvdh\Reflection\DocBlock;
use Barryvdh\Reflection\DocBlock\Type\Collection;
use Barryvdh\Reflection\DocBlock\Serializer as DocBlockSerializer;


class ClassDoc extends ReflectionClass
{
    /** @var \phpDocumentor\Reflection\DocBlock */
    protected $docBlock;

    public function __construct($className)
    {
        parent::__construct($className);
        $this->docBlock = new DocBlock($this, new DocBlock\Context($this->getNamespaceName()));
    }

    protected $ensureTags = [
        'lines'      => [
            // $tag_line => $tag
        ],
        'methods'    => [
            // $methodName => $methodTag
        ],
        'properties' => [
            // $propertyName => $propertyTag
        ],
    ];

    public function ensure(string $name, string $content)
    {
        $tagLine                      = "@{$name} {$content}";
        $tag                          = DocBlock\Tag::createInstance($tagLine);
        $this->ensureTags[ $tagLine ] = $tag;
        return $tag;
    }

    /**
     * @param string                $methodName
     * @param string|string[]|array $types
     * @param string|null           $arguments
     * @param string|null           $description
     * @return \Barryvdh\Reflection\DocBlock\Tag\MethodTag
     */
    public function ensureStaticMethod(string $methodName, $types = '', string $arguments = null, string $description = null)
    {
        $static = true;
        return $this->ensureMethodTag($methodName, $types, compact('static', 'arguments', 'description'));
    }

    /**
     * @param string                $methodName
     * @param string|string[]|array $types
     * @param string|null           $arguments
     * @param string|null           $description
     * @return \Barryvdh\Reflection\DocBlock\Tag\MethodTag
     */
    public function ensureMethod(string $methodName, $types = '', string $arguments = null, string $description = null)
    {
        return $this->ensureMethodTag($methodName, $types, compact('arguments', 'description'));
    }

    /**
     * @param string                $methodName
     * @param string|string[]|array $types
     * @param array                 $options = [
     *                                       'arguments' => '',
     *                                       'docblock' => '',
     *                                       'description' => '',
     *                                       'static' => false
     *                                       ]
     * @return \Barryvdh\Reflection\DocBlock\Tag\MethodTag
     */
    public function ensureMethodTag(string $methodName, $types = '', array $options = [])
    {
        $type = implode(Collection::OPERATOR_OR, Arr::wrap($types));
        $o    = collect($options);
        $tag  = DocBlock\Tag\MethodTag::createInstance('@method', $this->docBlock)
            ->setMethodName($methodName)
            ->setType($type);

        $tag->setDescription($o->get('description', null));
        $tag->setDocBlock($o->get('docblock', $this->docBlock));
        $tag->setIsStatic($o->get('static', false));
        $tag->setArguments($o->get('arguments', ''));

        $this->ensureTags[ 'methods' ][ $methodName ] = $tag;
        return $tag;
    }

    public function ensureProperty(string $propertyName, $types = '', string $description = null)
    {
        return $this->ensurePropertyTag($propertyName, $types, compact('description'));
    }

    public function ensurePropertyTag(string $propertyName, $types = '', array $options = [])
    {
        $type = implode(Collection::OPERATOR_OR, Arr::wrap($types));
        $o    = collect($options);
        $tag  = DocBlock\Tag\PropertyTag::createInstance('@property', $this->docBlock)
            ->setVariableName($propertyName)
            ->setType($type);

        $tag->setDescription($o->get('description', null));
        $tag->setDocBlock($o->get('docblock', $this->docBlock));

        $this->ensureTags[ 'properties' ][ $propertyName ] = $tag;
        return $tag;
    }

    public function process()
    {
        if ( ! $this->docBlock->getText()) {
            $this->docBlock->setText($this->getName());
        }
        $tags       = $this->getTags();
        $methods    = $tags->methods();
        $properties = $tags->properties();
        foreach (compact('methods', 'properties') as $name => $tags) {
            $ensureTags = $this->ensureTags[ $name ];
            foreach ($ensureTags as $ensureName => $ensureTag) {
                if ( ! $tags->has($ensureName)) {
                    $this->docBlock->appendTag($ensureTag);
                }
            }
        }

        $serializer = new DocBlockSerializer();
        $serializer->getDocComment($this->docBlock);

        $docComment         = $serializer->getDocComment($this->docBlock);
        return new Result($this, $docComment);
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

    public function getName()
    {
        return Str::ensureLeft(parent::getName(), '\\');
    }

    public function getNameArray()
    {
        return $this->getName() . '[]';
    }

    public function __toString()
    {
        return $this->getName();
    }

}