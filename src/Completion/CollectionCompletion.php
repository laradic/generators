<?php

namespace Laradic\Generators\Completion;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Laradic\Generators\DocBlock\DocBlockGenerator;

class CollectionCompletion implements CompletionInterface
{
    /** @var string */
    protected $collection;

    /** @var string */
    protected $item;

    const RESOLVE = '__RESOLVE__';

    //@formatter:off
    public static $proxies = [
        'average', 'avg', 'contains', 'each', 'every', 'filter', 'first',
        'flatMap', 'groupBy', 'keyBy', 'map', 'max', 'min', 'partition',
        'reject', 'some', 'sortBy', 'sortByDesc', 'sum', 'unique',
    ];//@formatter:on

    public static $returnsItem = [
        'findBy',
        'find',
        'get',
        'firstWhere',
        'first',
        'last',
        'find',
        'findBy',
        'pull',
        'shift',
        'pop',
    ];
    public static $returnsSelf = [
        'filterBy',
        'whereLike',
        'load',
        'loadCount',
        'loadMissing',
        'loadMorph',
        'contains',
//    'modelKeys',
        'merge',
        'map',
//    'fresh',
        'diff',
        'intersect',
        'unique',
        'only',
        'except',
        'makeHidden',
        'makeVisible',
        'getDictionary',
//    'pluck',
//    'keys',
        'zip',
        'collapse',
        'flatten',
        'flip',
        'times',
        'all',
//    'avg',
//    'average',
//    'median',
//    'mode',
//    'some',
        'containsStrict',
        'crossJoin',
//    'dd',
//    'dump',
        'diffUsing',
        'diffAssoc',
        'diffAssocUsing',
        'diffKeys',
        'diffKeysUsing',
        'duplicates',
        'duplicatesStrict',
        'each',
        'eachSpread',
        'every',
        'filter',
        'when',
        'whenEmpty',
        'whenNotEmpty',
        'unless',
        'unlessEmpty',
        'unlessNotEmpty',
        'where',
        'whereStrict',
        'whereIn',
        'whereInStrict',
        'whereBetween',
        'whereNotBetween',
        'whereNotIn',
        'whereNotInStrict',
        'whereInstanceOf',
        'forget',
        'groupBy',
        'keyBy',
        'intersectByKeys',
//    'isEmpty',
//    'isNotEmpty',
        'join',
        'mapWithKeys',
        'flatMap',
        'mapInto',
        'mergeRecursive',
        'combine',
        'union',
//    'min',
//    'nth',
//    'forPage',
//    'partition',
//    'pipe',
//    'pop',
        'prepend',
        'push',
        'concat',

        'put',
        'random',
        'reduce',
        'reject',
        'replace',
        'replaceRecursive',
        'reverse',
        'search',
        'shuffle',
//    'slice',
//    'split',
        'chunk',
        'sort',
        'sortBy',
        'sortByDesc',
        'sortKeys',
        'sortKeysDesc',
//    'splice',
        'sum',
//    'take',
//    'tap',
        'transform',
        'uniqueStrict',
        'values',
//    'toArray',
//    'jsonSerialize',
//    'toJson',
//    'getIterator',
//    'getCachingIterator',
//    'count',
//    'countBy',
        'add',
    ];
    /** @var array */
    protected $exclude;

    public function __construct(string $collection, string $item, array $exclude = [])
    {
        $this->collection = $collection;
        $this->item       = $item;
        $this->exclude    = $exclude;
    }

    public function generate(DocBlockGenerator $generator)
    {
        $class      = $generator->class($this->collection);
        $collection = Str::ensureLeft($this->collection, '\\');
        $item       = Str::ensureLeft($this->item, '\\');
        $class->cleanTag('method');
        $methods = [
//            [ 'sortBy', [ $collection, $item . '[]' ], null ],
//            [ 'all', [ $item . '[]' ], null ],
//            [ 'first', [ $item . '[]' ], null ],

        ];
        foreach (static::$returnsSelf as $name) {
            $methods[] = [ $name, [ $collection, $item . '[]' ], null ];
        }
        foreach (static::$returnsItem as $name) {
            $methods[] = [ $name, [ $item ], null ];
        }
        $_methods = collect($class->getReflection()->getMethods(\ReflectionMethod::IS_PUBLIC))->map->getName();

        foreach ($methods as $key => $method) {
            list($name, $types, $parameters) = $method;
            if ($parameters === null) {
                if ($class->getReflection()->hasMethod($name)) {
                    $parameters           = $this->getMethodParams($class->getReflection()->getMethod($name));
                    $methods[ $key ][ 2 ] = $parameters;
                }
            }
        }

        foreach ($methods as $method) {
            list($name, $types, $parameters) = $method;
            if (in_array($name, $this->exclude)) {
                continue;
            }
            $types=Arr::wrap($types);
            $types=implode('|',$types);
            $class->ensureMethodTag('')->setMethodName($name)->setType($types)->setArguments($parameters);
        }
    }

    protected function getMethodParams(\ReflectionMethod $method)
    {
        $parameters = [];
        foreach ($method->getParameters() as $param) {
            $parameter = [];
            if ($param->hasType()) {
                $parameter[] = $param->getType()->getName();
            }
            $parameter[] = '$' . $param->getName();
            $isAvailable = $param->isDefaultValueAvailable();
            if ($isAvailable) {
                $parameter[] = '=';
                if ($param->isDefaultValueConstant()) {
                    $paramConstant = $param->getDefaultValueConstantName();
                    $namespace     = $param->getDeclaringClass()->getNamespaceName() . '\\';
                    $paramValue    = str_replace($namespace, '', $paramConstant);
                } else {
                    $paramValue = $param->getDefaultValue();
                    $paramValue = var_export($paramValue, true);  // oir json_encode($paramValue)
                }
                $parameter[] = $paramValue;
            }
            $parameters[] = implode(' ', $parameter);
        }
        return implode(', ', $parameters);
    }
}
