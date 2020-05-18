<?php

namespace Laradic\Generators\Doc;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Events\Dispatcher;
use Illuminate\Contracts\Container\Container;

class DocChainExecutor
{
    /** @var \Laradic\Generators\Doc\DocRegistry */
    protected $registry;

    /** @var \Laradic\Generators\Doc\DocSerializer */
    protected $serializer;

    /** @var array */
    protected $chain = [];

    /** @var \Illuminate\Contracts\Container\Container */
    protected $container;

    protected $isTransformed;
    /**
     * @var \Illuminate\Events\Dispatcher
     */
    protected $dispatcher;

    public function __construct(DocRegistry $registry, DocSerializer $serializer, Container $container)
    {
        $this->registry   = $registry;
        $this->serializer = $serializer;
        $this->container  = $container;
        $this->dispatcher = new Dispatcher();
    }

    public function run()
    {
        $this->transform();
        $this->serializer->writeToSourceFiles($this->registry->getClasses());
        return $this;
    }

    public function transform()
    {
        if ($this->isTransformed) {
            return $this;
        }
        $this->isTransformed = true;
        $this->callChainItems();
        $this->serializer->transform($this->registry->getClasses());
        return $this;
    }

    protected function callChainItems()
    {
        foreach ($this->chain as $item) {
            if ($item instanceof Closure) {
                $this->container->call($item, [ 'registry' => $this->registry ]);
                continue;
            }
            if (is_string($item)) {
                $item = $this->container->make($item);
            }
            $this->dispatcher->dispatch('call', [$item]);
            $this->container->call([ $item, 'handle' ], [ 'registry' => $this->registry ]);
        }
    }

    public function on($event, \Closure $listener)
    {
        $this->dispatcher->listen($event, $listener);
        return $this;
    }

    //region: Getter & Setters

    public function getRegistry()
    {
        return $this->registry;
    }

    public function setRegistry($registry)
    {
        $this->registry = $registry;
        return $this;
    }

    public function getSerializer()
    {
        return $this->serializer;
    }

    public function setSerializer($serializer)
    {
        $this->serializer = $serializer;
        return $this;
    }

    public function getChain()
    {
        return $this->chain;
    }

    public function setChain($chain)
    {
        $this->chain = $chain;
        return $this;
    }

    public function appendToChain($items)
    {
        $this->chain = array_merge($this->chain, Arr::wrap($items));
        return $this;
    }

    public function prependToChain($items)
    {
        $this->chain = array_merge(Arr::wrap($items), $this->chain);
        return $this;
    }

    //endregion


}