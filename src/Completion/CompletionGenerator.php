<?php


namespace Laradic\Generators\Completion;

use Illuminate\Support\Arr;
use Laradic\Support\MultiBench;
use Laradic\Generators\DocBlock\DocBlockGenerator;

class CompletionGenerator
{
    protected $pipes = [];

    protected $pipeline;

    public function __construct(array $pipes = [])
    {
        $this->pipes    = $pipes;
        $this->pipeline = new Pipeline(app());
    }

    public function generate()
    {
        MultiBench::on('completions')->mark('generate');
        $result = $this->pipeline
            ->send($this->createDocblockGenerator())
            ->through($this->pipes)
            ->via('generate')
            ->thenReturn();
        MultiBench::on('completions')->mark('generated')->mark('process');
        $result = $result->process()->all();
        MultiBench::on('completions')->mark('processed');
        return new ProcessedCompletions($result);
    }

    protected function createDocblockGenerator()
    {
        return new DocBlockGenerator();
    }

    public function before(\Closure $cb)
    {
        $this->pipeline->beforePipe($cb);
        return $this;
    }

    public function after(\Closure $cb)
    {
        $this->pipeline->afterPipe($cb);
        return $this;
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
