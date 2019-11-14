<?php

namespace Laradic\Generators\DocBlock\Command;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Laradic\Generators\DocBlock\Definition\DefinitionCollection;

class GenerateTempFiles
{
    use DispatchesJobs;

    /** @var \Laradic\Generators\DocBlock\Definition\DefinitionCollection|\Laradic\Generators\DocBlock\Definition\Definition[] */
    protected $definitions;

    public function __construct(DefinitionCollection $definitions)
    {
        $this->definitions = $definitions;
    }


    public function handle()
    {
        /** @var \Illuminate\Support\Collection|\SplTempFileObject[] $tempFiles */
        $tempFiles = $this->definitions->tempFiles();
        foreach ($this->definitions as $definition) {
            $reflection          = $definition->getReflection();
            $originalDocComment  = $reflection->getDocComment();
            $processedDocComment = $definition->getDocComment();
            if ($originalDocComment === $processedDocComment) {
                continue;
            }
            /** @var \SplTempFileObject $tempFile */
            $tempFile = $tempFiles->get($definition->getFile()->getPathname()); //->openFile('w');
            $content  = $tempFile->fread($tempFile->fstat()[ 'size' ]);
            if ($originalDocComment) {
                $content = str_replace($originalDocComment, $processedDocComment, $content);
            } else {
                $pos = $this->dispatchNow(new GetDefinitionStartLine($definition, $tempFile));
                $tempFile->seek($pos);
                $originalLine    = $tempFile->current();
                $replacementLine = "{$processedDocComment}\n{$originalLine}";
                $content         = substr_replace($content, $replacementLine, $pos, strlen($originalLine));
            }

            $tempFile->ftruncate(0);
            $tempFile->fwrite($content);
            $tempFile->rewind();
        }
        return $tempFiles;
    }

}