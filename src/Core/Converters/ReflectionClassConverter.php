<?php


namespace Laradic\Generators\Core\Converters;

use SplFileObject;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionParameter;
use Illuminate\Support\Str;
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

    public function handle()
    {
        return $this->convert();
    }

    public function convert()
    {
        $ref   = $this->reflection;
        $class = $this->class;

//        $class->setName($ref->getShortName());
//        if ($parent = $ref->getParentClass()) {
//            $class->setExtends($parent->getName());
//        }
//
//        foreach($this->getInterfaces() as $interface){
//            $class->addImplement($interface->getName());
//        }
        $class->setName($ref->getShortName(), null, $this->getInterfaceShortNames());
        if ($parent = $ref->getParentClass()) {
            $class->addUse($parent->getName());
            $class->getName()->setExtends($parent->getShortName());
        }
        collect($this->getInterfaces())->each(function (ReflectionClass $interface) {
            $this->class->addUse($interface->getName());
        });
        $class->setNamespace($ref->getNamespaceName());
        $filterByDeclaringClass = function (\Reflector $ref) {
            if ( ! method_exists($ref, 'getDeclaringClass')) {
                return true;
            }
            return $this->reflection->getName() === $ref->getDeclaringClass()->getName();
        };

        $constants = collect($ref->getReflectionConstants())->filter($filterByDeclaringClass)->map(function (ReflectionClassConstant $ref) {
            return $this->class->addConstant($ref->getName(), $ref->getValue());
        });
        $traits    = collect($ref->getTraits())->map(function (ReflectionClass $ref) {
            $this->class->addUse($ref->getName());
            return $this->class->addUseTrait($ref->getShortName());
        });

        $methods    = collect($ref->getMethods())
            ->filter($filterByDeclaringClass)
            ->filter(function(ReflectionMethod $ref){
                // filter out methods from traits
                $names = collect($this->reflection->getTraits())->map->getMethods()->flatten(1)->map->getName();
                return $names->contains($ref->getName()) === false;
            })
            ->map(function (ReflectionMethod $ref) {

                $method = $this->class->addMethod($ref->getName());
                $method->setAccess($this->getAccess($ref));
                $method->setStatic($ref->isStatic());
                $method->setAbstract($ref->isAbstract());
                $method->setFinal($ref->isFinal());
                $method->setBody($body = $this->getMethodBody($ref));
                $docComment = explode("\n", static::cleanInput($ref->getDocComment()));
                $method->setDocBlock($docComment);
                collect($ref->getParameters())->each(function (ReflectionParameter $ref) use ($method) {
                    $method->addArgument($ref->getName(), $ref->getType(), $this->getParameterDefault($ref));
                });
                $arguments = $method->getArguments();
                return $method;
            });
        $properties = collect($ref->getProperties())
            ->filter($filterByDeclaringClass)
            ->filter(function(ReflectionProperty $ref){
                // filter out properties from traits
                $names = collect($this->reflection->getTraits())->map->getProperties()->flatten(1)->map->getName();
                return $names->contains($ref->getName()) === false;
            })
            ->map(function (ReflectionProperty $ref) {

                $property      = $this->class->addProperty($ref->getName(), $this->getAccess($ref));
                $docComment = explode("\n", static::cleanInput($ref->getDocComment()));
                $property->setDocBlock($docComment);
                $property->setStatic($ref->isStatic());
                $property->setValue(data_get($this->reflection->getDefaultProperties(), $ref->getName(), null));
                return $property;
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

    protected function getInterfaces()
    {
        $interfaces = $this->reflection->getInterfaces();
        if ($parent = $this->reflection->getParentClass()) {
            foreach ($parent->getInterfaceNames() as $name) {
                unset($interfaces[ $name ]);
            }
        };
        return $interfaces;
    }

    protected function getInterfaceShortNames()
    {
        $shortNames = [];

        foreach ($this->getInterfaces() as $name => $ref) {
            if(in_array($ref->getShortName(), $shortNames, true)){
                $shortNames[] = Str::ensureLeft($ref->getName(), '\\');
            } else {
                $shortNames[] = $ref->getShortName();
            }
        }
        return $shortNames;
    }
}
