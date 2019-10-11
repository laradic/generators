<?php


namespace Laradic\Generators\DocBlock;


use ReflectionProperty;
use Barryvdh\Reflection\DocBlock;
use Barryvdh\Reflection\DocBlock\Serializer as DocBlockSerializer;

class PropertyDoc extends ReflectionProperty
{
    /**
     * @var \Barryvdh\Reflection\DocBlock
     */
    protected $docBlock;

    public function __construct($className, $propertyName)
    {
        parent::__construct($className, $propertyName);
        $this->docBlock = new DocBlock($this, new DocBlock\Context($this->getDeclaringClass()->getNamespaceName()));
    }

    protected $ensureTags = [];

    public function ensure(string $name, string $content)
    {
        $tagLine                      = "@{$name} {$content}";
        $tag                          = DocBlock\Tag::createInstance($tagLine);
        $this->ensureTags[ $tagLine ] = $tag;
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
        $lines = $tags->other();
        foreach (compact('methods', 'properties','lines') as $name => $tags) {
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
        return new ProcessedClassDoc($this, $docComment);
    }


}