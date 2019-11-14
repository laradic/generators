<?php

namespace Laradic\Generators\DocBlock\Definition;

use Illuminate\Support\Collection;

class DefinitionCollection extends Collection
{
    /** @var Definition[] */
    protected $items;


    /**
     * @param 'class'|'method'|'property' $type
     * @return mixed
     */
    public function type($type)
    {
        return $this->filter->isType($type);
    }

    public function process()
    {
        $this->each->process();
        return $this;
    }

    /** @return Collection|\SplFileInfo[] */
    public function files()
    {
        return $this->map->getFile()
            ->toBase()
            ->mapWithKeys(function (\SplFileInfo $file, $key) {
                return [ $file->getPathname() => $file ];
            });
    }

    /** @return Collection|\SplTempFileObject[] */
    public function tempFiles()
    {
        return $this->files()->map(function (\SplFileInfo $file, $pathName) {
            $content = file_get_contents($pathName);
            $tmpFile = new \SplTempFileObject();
            $tmpFile->fwrite($content);
            $tmpFile->rewind();
            return $tmpFile;
        });
    }

    public function transformContent()
    {
        foreach ($this->items as $definition) {
            $reflection          = $definition->getReflection();
            $originalDocComment  = $reflection->getDocComment();
            $processedDocComment = $definition->getDocComment();
            $content             = $definition->getContent();
            $lines               = explode("\n", $content);
            $needle              = '';
            $offset              = 0;
            if ($reflection instanceof \ReflectionClass) {
                $needle = 'class ' . $reflection->getShortName();
            } elseif ($reflection instanceof \ReflectionMethod) {
                $needle = 'function ' . $reflection->getShortName();
            } elseif ($reflection instanceof \ReflectionProperty) {
                $startLine = $reflection->getDeclaringClass()->getStartLine();
                $offset    = strlen(implode("\n", array_slice($lines, 0, $startLine)));
                $mod       = $reflection->getModifiers();
                $needle    = '$' . $reflection->getName();
            }
            $pos = strpos($content, $needle, $offset);
        }
    }

    public function toBase()
    {
        return new  Collection($this->items);
    }
}