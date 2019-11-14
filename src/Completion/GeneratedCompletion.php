<?php


namespace Laradic\Generators\Completion;


use Illuminate\Foundation\Bus\DispatchesJobs;
use Laradic\Generators\DocBlock\Command\GenerateTempFiles;
use Laradic\Generators\DocBlock\Command\WriteTempFilesToSource;

class GeneratedCompletion
{
    use DispatchesJobs;

    /** @var \Laradic\Generators\DocBlock\Definition\ClassDefinition[] */
    protected $results;

    /**
     * GeneratedCompletion constructor.
     *
     * @param \Laradic\Generators\DocBlock\ProcessedClassDefinition[] $results
     */
    public function __construct(array $results)
    {
        $this->results = $results;
    }

    /**
     * @return \Illuminate\Support\Collection|\Laradic\Generators\DocBlock\ProcessedClassDefinition[]
     */
    public function getResults()
    {
        return collect($this->results);
    }

    public function writeToSourceFiles()
    {
        foreach($this->results as $result){
            /** @var \Illuminate\Support\Collection|\SplTempFileObject[] $tempFiles */
            $tempFiles = $this->dispatchNow(new GenerateTempFiles($definitions = $result->collect()));
            $this->dispatchNow(new WriteTempFilesToSource($definitions,$tempFiles));
        }
    }

    public function cleanSourceFiles()
    {
        foreach($this->results as $result){
            $class = $result->getClass();
            file_put_contents($class->getFileName(), $result->clearClassDoc($result->content()));
        }

    }


    public function combineForCompletionFile()
    {
        $lines = ['<?php'];
        foreach ($this->results as $result) {
            $class   = $result->getClass();
            $lines[] = "namespace {$class->getNamespaceName()} {";
            $lines[] = $result->getDoc();
            $lines[] = "class {$class->getShortName()}{}";
            $lines[] = '}';
        }

        return implode(PHP_EOL, $lines);

    }

    public function writeToCompletionFile($path)
    {
        if (path_is_relative($path)) {
            $path = base_path($path);
        }
        file_put_contents($path, $this->combineForCompletionFile());
        return $path;
    }

}