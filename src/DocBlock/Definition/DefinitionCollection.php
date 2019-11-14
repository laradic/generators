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

    public function toBase()
    {
        return new  Collection($this->items);
    }
}