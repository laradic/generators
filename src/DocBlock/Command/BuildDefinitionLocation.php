<?php

namespace Laradic\Generators\DocBlock\Command;

use Laradic\Support\Spl\FileSelectAction;
use Laradic\Support\Spl\FileSearchAction;
use Barryvdh\Reflection\DocBlock\Location;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Laradic\Generators\DocBlock\Definition\Definition;

class BuildDefinitionLocation
{
    use DispatchesJobs;

    /** @var \Barryvdh\Reflection\DocBlock\Location[] */
    protected static $cache = [];

    /** @var \Laradic\Generators\DocBlock\Definition\Definition */
    protected $definition;

    public function __construct(Definition $definition)
    {
        $this->definition = $definition;
    }

    protected function handle()
    {
        $reflection = $this->definition->getReflection();
        if ($reflection->getDocComment() === false) {
            return;
        }
//        if ($this->hasCached()) {
//            return $this->getCached();
//        }

        $file             = $this->definition->getFile()->openFile();
        $select           = FileSelectAction::make($file);
        $line             = $this->dispatchNow(new GetDefinitionStartLine($this->definition, $file));
        $docComment       = $reflection->getDocComment();
        $docCommentLines  = substr_count($docComment, "\n");
        $docStartLine     = FileSearchAction::make($file)
            ->startAt($line)
            ->upwards()
            ->returnFirstMatch()
            ->matchesExpression('/\/\*\*/')
            ->getResult();
        $docTextStartLine = FileSearchAction::make($file)
            ->startAt($docStartLine)
            ->returnFirstMatch()
            ->matchesExpression('/\s*\*\s*\w.*/')
            ->getResult();

        $file->seek($docTextStartLine);
        $str      = $file->current();
        $total    = preg_match_all('/\*(\s*)/', $str, $matches);
        $column   = $total === 1 ? strlen($matches[ 1 ][ 0 ]) - 1 : 0;
        $location = new Location($docTextStartLine, $column);
//        $this->cache($location);
        return $location;
    }

    protected function hasCached()
    {
        return array_key_exists($this->definition->getFile()->getPathname(), static::$cache);
    }

    protected function cache(Location $location)
    {
        static::$cache[ $this->definition->getReflection()->getFileInfo()->getPathname() ] = $location;
    }

    /**
     * @return \Barryvdh\Reflection\DocBlock\Location
     */
    protected function getCached()
    {
        return static::$cache[ $this->definition->getFile()->getPathname() ];
    }
}