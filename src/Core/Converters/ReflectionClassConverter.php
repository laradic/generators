<?php


namespace Laradic\Generators\Core\Converters;

use SplFileObject;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionParameter;
use ReflectionClassConstant;
use Laradic\Generators\Core\Elements\ClassElement;
use function compact;
use function collect;
use function explode;

class ReflectionClassConverter
{
    protected $reflection;
    protected $class;

    public function __construct(ReflectionClass $reflection)
    {
        $this->reflection = $reflection;
        $this->class      = new ClassElement();
    }

    public function convert()
    {
        $ref        = $this->reflection;
        $class      = $this->class;
        $interfaces = collect($ref->getInterfaces());


        $class->setName($ref->getShortName(), $ref->getParentClass(), $this->getInterfaceShortNames());
        $interfaces->each(function (ReflectionClass $interface) {
            $this->class->addUse($interface->getName());
        });
        $class->setNamespace($ref->getNamespaceName());

        $constants  = collect($ref->getReflectionConstants())->map(function (ReflectionClassConstant $ref) {
            return $this->class->addConstant($ref->getName(), $ref->getValue());
        });
        $traits     = collect($ref->getTraits())->map(function (ReflectionClass $ref) {
            $this->class->addUse($ref->getName());
            return $this->class->addUseTrait($ref->getShortName());
        });
        $methods    = collect($ref->getMethods())->map(function (ReflectionMethod $ref) {
            $value = $this->class->addMethod($ref->getName());
            $value->setAccess($this->getAccess($ref));
            $value->setStatic($ref->isStatic());
            $value->setAbstract($ref->isAbstract());
            $value->setFinal($ref->isFinal());
            $value->setBody($body = $this->getMethodBody($ref));
            $docComment = explode("\n", static::cleanInput($ref->getDocComment()));
            $value->setDocBlock($docComment);
            collect($ref->getParameters())->each(function (ReflectionParameter $ref) use ($value) {
                $value->addArgument($ref->getName(), $ref->getType(), $this->getParameterDefault($ref));
            });
            $arguments = $value->getArguments();
            return $value;
        });
        $properties = collect($ref->getProperties())->map(function (ReflectionProperty $ref) {
            $value      = $this->class->addProperty($ref->getName(), $this->getAccess($ref));
            $docComment = explode("\n", static::cleanInput($ref->getDocComment()));
            $value->setDocBlock($docComment);
            $value->setStatic($ref->isStatic());
            return $value;
        });
        $data       = collect(compact('traits', 'constants', 'methods', 'properties'));
        $arr        = $data->toArray();

        return $class;
    }

    protected function getParameterDefault(ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueAvailable()) {
            if ($parameter->isDefaultValueConstant()) {
                return $parameter->getDefaultValueConstantName();
            }
            return $parameter->getDefaultValue();
        }
        return null;
    }

    /**
     * Strips the asterisks from the DocBlock comment.
     *
     * @param string $comment String containing the comment text.
     * @return string
     */
    public static function cleanInput($comment)
    {
        $comment = trim(
            preg_replace(
                '#[ \t]*(?:\/\*\*|\*\/|\*)?[ \t]{0,1}(.*)?#u',
                '$1',
                $comment
            )
        );

        // reg ex above is not able to remove */ from a single line docblock
        if (substr($comment, -2) == '*/') {
            $comment = trim(substr($comment, 0, -2));
        }

        // normalize strings
        $comment = str_replace([ "\r\n", "\r" ], "\n", $comment);

        return $comment;
    }

    protected function getMethodBody(ReflectionMethod $ref)
    {
        $lines = collect();
        $file  = new SplFileObject($ref->getFileName(), 'r');
        $start = $ref->getStartLine();
        $end   = $ref->getEndLine() - 1;
        $file->seek($start);
        while ($file->key() < $end) {
            $lines->add($file->getCurrentLine());
            $file->next();
        }
        $body = $lines->implode(PHP_EOL);
        return $body;
    }

    /**
     * @param ReflectionProperty|ReflectionMethod $ref
     * @return string
     */
    protected function getAccess($ref)
    {
        if ($ref->isPublic()) {
            return 'public';
        }
        if ($ref->isProtected()) {
            return 'protected';
        }
        return 'private';
    }

    protected function getInterfaceShortNames()
    {
        $shortNames = [];
        foreach ($this->reflection->getInterfaces() as $name => $ref) {
            $shortNames[] = $ref->getShortName();
        }
        return $shortNames;
    }
}
