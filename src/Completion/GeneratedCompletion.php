<?php


namespace Laradic\Generators\Completion;


class GeneratedCompletion
{
    /** @var \Laradic\Generators\DocBlock\ProcessedClassDoc[] */
    protected $results;

    /**
     * GeneratedCompletion constructor.
     *
     * @param \Laradic\Generators\DocBlock\ProcessedClassDoc[] $results
     */
    public function __construct(array $results)
    {
        $this->results = $results;
    }

    /**
     * @return \Illuminate\Support\Collection|\Laradic\Generators\DocBlock\ProcessedClassDoc[]
     */
    public function getResults()
    {
        return collect($this->results);
    }

    public function writeToSourceFiles()
    {
        foreach($this->results as $result){
            $class = $result->getClass();
            file_put_contents($class->getFileName(), $result->content());
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