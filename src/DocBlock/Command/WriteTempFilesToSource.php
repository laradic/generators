<?php

namespace Laradic\Generators\DocBlock\Command;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Laradic\Generators\DocBlock\Definition\DefinitionCollection;

class WriteTempFilesToSource
{
    use DispatchesJobs;

    /** @var \Laradic\Generators\DocBlock\Definition\DefinitionCollection|\Laradic\Generators\DocBlock\Definition\Definition[] */
    protected $definitions;
    /** @var \Illuminate\Support\Collection|\SplTempFileObject[] */
    protected $tempFiles;

    public function __construct(DefinitionCollection $definitions, Collection $tempFiles)
    {
        $this->definitions = $definitions;
        $this->tempFiles = $tempFiles;
    }

    public function handle()
    {
        $files = $this->definitions->files();
        foreach($this->tempFiles as $pathName => $tempFile){
            /** @var \SplFileObject $file */
            $file=$files->get($pathName)->openFile('w');
            $content = $tempFile->fread($tempFile->fstat()[ 'size' ]);
            $file->ftruncate(0);
            $file->fwrite($content);
        }
    }

}