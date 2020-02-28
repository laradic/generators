<?php /** @noinspection PhpUnusedAliasInspection */

namespace Laradic\Generators\Doc\Doc;

use Barryvdh\Reflection\DocBlock\Tag;
use Illuminate\Support\Arr;
use Laradic\Generators\Doc\DocBlock;
use ReflectionProperty;

/**
 * @method ReflectionProperty getReflection()
 */
class PropertyDoc extends BaseDoc
{
    /** @var string */
    protected $propertyName;

   /** @var ClassDoc */
    protected $classDoc;

    protected static $properties = [];

    protected function __construct(ClassDoc $classDoc, string $propertyName)
    {
        $this->classDoc   = $classDoc;
        $this->className  = $classDoc->getClassName();
        $this->propertyName = $propertyName;
        $this->reflection = new ReflectionProperty($this->className, $propertyName);
        $this->docblock   = new DocBlock($this->reflection);
    }

    public static function make(ClassDoc $classDoc, string $propertyName)
    {
        $fqns = $classDoc->getClassName() . '::$' . $propertyName ;
        if ( ! array_key_exists($fqns, static::$properties)) {
            static::$properties[ $fqns ] = new static($classDoc, $propertyName);
        }
        return static::$properties[ $fqns ];
    }

    /**
     * @param        $types
     * @param string $description
     *
     * @return \Barryvdh\Reflection\DocBlock\Tag\VarTag
     */
    public function ensureAndReturnVarTag($types, $description = '')
    {
        $this->resolveType($types);
        $this->docblock->getTagsByName('var')->deleteFrom($this->docblock);
        $types = implode('|', Arr::wrap($types));
        $tag   = Tag::createInstance("@var {$types} {$description}");
        $this->docblock->appendTag($tag);
        return $tag;
    }

    public function ensureVar($types, $description = '')
    {
        $this->ensureAndReturnVarTag($types, $description);
        return $this;
    }

    public function getPropertyName()
    {
        return $this->propertyName;
    }

    public function getClassDoc()
    {
        return $this->classDoc;
    }


}
