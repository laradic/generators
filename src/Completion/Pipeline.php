<?php

namespace Laradic\Generators\Completion;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Responsable;

class Pipeline extends \Illuminate\Pipeline\Pipeline
{

    public function then(Closure $destination)
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes), $this->carry(), $this->prepareDestination($destination)
        );

        return $pipeline($this->passable);
    }
    public function _then(Closure $destination)
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes), $this->carry(), $this->prepareDestination($destination)
        );

        return $pipeline($this->passable);
    }

    /**
     * Run the pipeline and return the result.
     *
     * @return \Laradic\Generators\DocBlock\DocBlockGenerator
     */
    public function thenReturn()
    {
        return $this->then(function ($passable) {
            return $passable;
        });
    }

    /**
     * Get the final piece of the Closure onion.
     *
     * @param  \Closure  $destination
     * @return \Closure
     */
    protected function prepareDestination(Closure $destination)
    {
        return function ($passable) use ($destination) {
            return $destination($passable);
        };
    }

    /**
     * Get a Closure that represents a slice of the application onion.
     *
     * @return \Closure
     */
    protected function carry()
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                $bcstack = function($a){
                    // temporary bc function
                    return;
                };
                if (is_callable($pipe)) {
                    // If the pipe is an instance of a Closure, we will just call it directly but
                    // otherwise we'll resolve the pipes out of the container and call it with
                    // the appropriate method and arguments, returning the results back out.
//                    return $pipe($passable, $stack);
                    return $pipe($passable, $bcstack);
                } elseif (! is_object($pipe)) {
                    [$name, $parameters] = $this->parsePipeString($pipe);

                    // If the pipe is a string we will parse the string and resolve the class out
                    // of the dependency injection container. We can then build a callable and
                    // execute the pipe function giving in the parameters that are required.
                    $pipe = $this->getContainer()->make($name);

//                    $parameters = array_merge([$passable, $stack], $parameters);
                    $parameters = array_merge([$passable, $bcstack], $parameters);
                } else {
                    // If the pipe is already an object we'll just make a callable and pass it to
                    // the pipe as-is. There is no need to do any extra parsing and formatting
                    // since the object we're given was already a fully instantiated object.
//                    $parameters = [$passable, $stack];
                    $parameters = [$passable, $bcstack];
                }

                $this->runCallbacks($this->beforeCallbacks, $pipe);
                method_exists($pipe, $this->method)
                    ? $pipe->{$this->method}(...$parameters)
                    : $pipe(...$parameters);
                $this->runCallbacks($this->afterCallbacks, $pipe);
                $response = $stack($passable);

                return $response instanceof Responsable
                    ? $response->toResponse($this->getContainer()->make(Request::class))
                    : $response;

//                $response = method_exists($pipe, $this->method)
//                    ? $pipe->{$this->method}(...$parameters)
//                    : $pipe(...$parameters);
//
//                return $response instanceof Responsable
//                    ? $response->toResponse($this->getContainer()->make(Request::class))
//                    : $response;
            };
        };
    }

    /**
     * @param Closure[] $callbacks
     * @return void
     */
    public function runCallbacks($callbacks, ...$params)
    {
        foreach($callbacks as $callback){
            $callback(...$params);
        }
    }
    protected $beforeCallbacks = [];
    public function beforePipe(Closure $callback)
    {
        $this->beforeCallbacks[] =$callback;
    }
    protected $afterCallbacks = [];
    public function afterPipe(Closure $callback)
    {
        $this->afterCallbacks[] =$callback;
    }

}