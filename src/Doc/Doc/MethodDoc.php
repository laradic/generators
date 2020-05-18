<?php /** @noinspection PhpUnusedAliasInspection */

namespace Laradic\Generators\Doc\Doc;

use Barryvdh\Reflection\DocBlock\Tag;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laradic\Generators\Doc\DocBlock;
use ReflectionMethod;

/**
 * @method ReflectionMethod getReflection()
 */
class MethodDoc extends BaseDoc
{
    /** @var string */
    protected $methodName;

    /** @var ClassDoc */
    protected $classDoc;

    protected static $methods = [];

    /** @noinspection MagicMethodsValidityInspection */
    protected function __construct(ClassDoc $classDoc, string $methodName)
    {
        $this->classDoc   = $classDoc;
        $this->className  = $classDoc->getClassName();
        $this->methodName = $methodName;
        $this->reflection = new ReflectionMethod($this->className, $methodName);

        $this->docblock= new DocBlock($this->reflection);
        $this->docblock->setDoc($this);
    }

    public static function make(ClassDoc $classDoc, string $methodName)
    {
        $fqns = $classDoc->getClassName() . '::' . $methodName . '()';
        if ( ! array_key_exists($fqns, static::$methods)) {
            static::$methods[ $fqns ] = new static($classDoc, $methodName);
        }
        return static::$methods[ $fqns ];
    }

    public function ensureAndReturnParamTag($name, $types, $description = '')
    {
        $this->resolveType($types);
        $name = Str::ensureLeft($name, '$');
        $this->docblock->getParamTags()->whereVariableName($name)->deleteFrom($this->docblock);
        $types = implode('|', Arr::wrap($types));
        $tag   = Tag::createInstance("@param {$types} {$name} {$description}");
        $this->docblock->appendTag($tag);
        return $tag;
    }

    public function ensureParam($name, $types, $description = '')
    {
        $this->ensureAndReturnParamTag($name, $types, $description);
        return $this;
    }
    public function ensureAndReturnReturnTag($types, $description = '')
    {
        $this->resolveType($types);
        $this->docblock->getTagsByName('return')->deleteFrom($this->docblock);
        $types = implode('|', Arr::wrap($types));
        $tag   = Tag::createInstance("@return {$types} {$description}");
        $this->docblock->appendTag($tag);
        return $tag;
    }

    public function ensureReturn($types, $description = '')
    {
        $this->ensureAndReturnReturnTag($types, $description);
        return $this;
    }

    public function getMethodName()
    {
        return $this->methodName;
    }

    public function getClassDoc()
    {
        return $this->classDoc;
    }
}
