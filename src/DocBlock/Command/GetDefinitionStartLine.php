<?php

namespace Laradic\Generators\DocBlock\Command;

use SplFileObject;
use Laradic\Support\Spl\FileSearchAction;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Laradic\Generators\DocBlock\Definition\Definition;

class GetDefinitionStartLine
{
    use DispatchesJobs;
    /** @var \Laradic\Generators\DocBlock\Definition\Definition */
    protected $definition;
    /** @var \SplFileObject|null */
    protected $file;

    public function __construct(Definition $definition, ?SplFileObject $file = null)
    {
        $this->definition = $definition;
        $this->file = $file;
    }

    public function handle()
    {
        return $this->getDefinitionFileStartLine($this->definition, $this->file);
    }

    protected function getDefinitionFileStartLine(Definition $definition, \SplFileObject $file = null)
    {
        $reflection = $definition->getReflection();
        $file       = $file ?: $definition->getFile()->openFile('r');
        if ($reflection instanceof \ReflectionProperty) {
            $line = FileSearchAction::make($file)
                ->startAt($reflection->getDeclaringClass()->getStartLine())
                ->downwards()
                ->returnFirstMatch()
                ->matchesExpression('/.*(public|protected|private).*\\$' . $reflection->getName() . '/')
                ->getResult();
        } elseif ($reflection instanceof \ReflectionMethod) {
            $line = FileSearchAction::make($file)
                ->startAt($reflection->getStartLine() + 1)
                ->upwards()
                ->returnFirstMatch()
                ->matchesExpression('/.*function ' . $reflection->getShortName() . '.*\(/')
                ->getResult();
        } elseif ($reflection instanceof \ReflectionClass) {
            $line = FileSearchAction::make($file)
                ->startAt($reflection->getStartLine() + 1)
                ->upwards()
                ->returnFirstMatch()
                ->matchesExpression('/.*(class|interface|trait).*' . $reflection->getShortName() . '/')
                ->getResult();
        }
        $file->rewind();
        return $line;
    }
}