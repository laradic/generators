<?php /** @noinspection PhpUnusedAliasInspection */

namespace Laradic\Generators\Doc\Doc;

use ReflectionClass;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Barryvdh\Reflection\DocBlock\Tag;

/**
 * @method ReflectionClass getReflection()
 */
class ClassDoc extends BaseDoc
{
    /** @var ClassDoc[] */
    protected static $classes = [];

    /** @var \Laradic\Generators\Doc\Doc\PropertyDoc[] */
    protected $properties = [];

    /** @var \Laradic\Generators\Doc\Doc\MethodDoc[] */
    protected $methods = [];

    /** @noinspection MagicMethodsValidityInspection */
    public function __construct(string $className)
    {
        $this->className  = $className;
        $this->reflection = new ReflectionClass($className);
        $this->docblock   = $this->makeDocBlock();
    }

    public static function make(string $className)
    {
        if ( ! array_key_exists($className, static::$classes)) {
            static::$classes[ $className ] = new static($className);
        }
        return static::$classes[ $className ];
    }

    public static function getClasses()
    {
        return static::$classes;
    }

    public function getMethod($name)
    {
        if ( ! $this->reflection->hasMethod($name)) {
            throw new \RuntimeException("Method [{$name}] does not exist on class {$this->className}");
        }
        if ( ! array_key_exists($name, $this->methods)) {
            $this->methods[ $name ] = MethodDoc::make($this, $name);
        }
        return $this->methods[ $name ];
    }

    public function getProperty($name)
    {
        if ( ! $this->reflection->hasProperty($name)) {
            throw new \RuntimeException("Property [{$name}] does not exist on class {$this->className}");
        }
        if ( ! array_key_exists($name, $this->properties)) {
            $this->properties[ $name ] = PropertyDoc::make($this, $name);
        }
        return $this->properties[ $name ];
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param string          $name
     * @param string|string[] $types
     * @return \Laradic\Generators\DocBlock\Tags\MixinTag
     */
    public function ensureAndReturnMixinTag($reference, $deleteOtherMixins = false)
    {
        $this->resolveType($reference);
        $mixins = $this->docblock->getMixinTags();
        if ( ! $deleteOtherMixins) {
            $mixins = $mixins->whereReference($reference);
        }
        $mixins->deleteFrom($this->docblock);

        $tag = Tag::createInstance("@mixin {$reference}");
        $this->docblock->appendTag($tag);
        return $tag;
    }

    public function ensureMixin($reference, $deleteOtherMixins = false)
    {
        $this->ensureAndReturnMixinTag($reference, $deleteOtherMixins);
        return $this;
    }

    /**
     * @param string          $name
     * @param string|string[] $types
     * @param null            $description
     * @return \Barryvdh\Reflection\DocBlock\Tag\PropertyTag
     */
    public function ensureAndReturnPropertyTag($name, $types, $description = null)
    {
        $this->resolveType($types);
        $name = Str::ensureLeft($name, '$');
        $this->docblock->getPropertyTags()->whereVariableName($name)->deleteFrom($this->docblock);
        $types = implode('|', Arr::wrap($types));
        $tag   = Tag::createInstance("@property {$types} {$name}");
        if ($description !== null) {
            $tag->setDescription($description);
        }
        $this->docblock->appendTag($tag);
        return $tag;
    }

    public function ensureProperty($name, $types, $description = null)
    {
        $this->ensureAndReturnPropertyTag($name, $types, $description);
        return $this;
    }

    /**
     * @param string          $name
     * @param string|string[] $types
     * @param string|string[] $arguments
     * @param bool            $isStatic
     * @return \Barryvdh\Reflection\DocBlock\Tag\MethodTag
     */
    public function ensureAndReturnMethodTag($name, $types, $arguments = '', bool $isStatic = false, $description = null)
    {
        if (is_string($types)) {
            $types = explode('|', $types);
        }
        $this->resolveType($types);
        $this->resolveArguments($arguments);
        $this->docblock->getMethodTags()->whereMethodName($name)->deleteFrom($this->docblock);
        $types  = implode('|', Arr::wrap($types));
        $static = $isStatic ? 'static ' : '';
        $tag    = Tag::createInstance("@method {$static}{$types} {$name}({$arguments})");
        if ($description !== null) {
            $tag->setDescription($description);
        }
        $this->docblock->appendTag($tag);
        return $tag;
    }

    public function ensureAndReturnStaticMethodTag($name, $types, string $arguments = '', $description = null)
    {
        return $this->ensureAndReturnMethodTag($name, $types, $arguments, true, $description);
    }

    public function ensureMethod($name, $types, string $arguments = '', $description = null)
    {
        $this->ensureAndReturnMethodTag($name, $types, $arguments, false, $description);
        return $this;
    }

    public function ensureStaticMethod($name, $types, string $arguments = '', $description = null)
    {
        $this->ensureAndReturnStaticMethodTag($name, $types, $arguments, $description);
        return $this;
    }

    public function getFile()
    {
        return new \SplFileInfo($this->getReflectionFileName());
    }

    /** @var \SplTempFileObject */
    protected $temporaryFile;

    public function getTemporaryFile($fresh = false)
    {
        if ($this->temporaryFile === null || $fresh) {
            $content             = file_get_contents($this->getFile()->getPathname());
            $this->temporaryFile = new \SplTempFileObject();
            $this->temporaryFile->fwrite($content);
            $this->temporaryFile->rewind();
        }
        return $this->temporaryFile;
    }

    public function getTemporaryFileContent()
    {
        $tempFile = $this->getTemporaryFile();
        $content  = $tempFile->fread($tempFile->fstat()[ 'size' ]);
        $tempFile->rewind();
        return $content;
    }

    public function writeTemporaryFileTo($pathName)
    {
        file_put_contents($pathName, $this->getTemporaryFileContent());
        return $this;
    }

    public function getReflectionFileName()
    {
        if (method_exists($this->reflection, 'getFileName')) {
            return $this->reflection->getFileName();
        }
        return $this->reflection->getDeclaringClass()->getFileName();
    }

    /**
     * @inheritDoc
     */
    public function getClassDoc()
    {
        return $this;
    }
}
