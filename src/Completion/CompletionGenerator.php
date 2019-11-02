<?php


namespace Laradic\Generators\Completion;


use Illuminate\Support\Arr;
use Laradic\Generators\DocBlock\DocBlockGenerator;

class CompletionGenerator
{
    protected $pipes = [];

    public function __construct(array $pipes = [])
    {
        $this->pipes = $pipes;
    }


    public function generate()
    {
        $pipe      = new Pipeline(app());
        $generator = new DocBlockGenerator();
        /** @var DocBlockGenerator $result */
        $result = $pipe->send($generator)
            ->through($this->pipes)
            ->via('generate')
            ->thenReturn();

        return new GeneratedCompletion($generator->process()->all());
    }

    public function append($pipes)
    {
        $this->pipes = array_merge($this->pipes, Arr::wrap($pipes));
        return $this;
    }

    public function prepend($pipes)
    {
        $this->pipes = array_merge(Arr::wrap($pipes), $this->pipes);
        return $this;
    }

    public function reset()
    {
        $this->pipes = [];
        return $this;
    }
}