<?php


namespace Laradic\Generators\Core\Traits;

use Illuminate\Support\Collection;

/**
 * @property string $defaultCollection
 * @property array  $collections
 */
trait InitsCollections
{
//    protected $defaultCollection = ElementCollection::class;
//    protected $collections = [
//        'asdf' => SuperCollection::class,
//        'bfoo',
//    ];

    private function getCollections()
    {
        if ($this->collections !== null && is_array($this->collections)) {
            return $this->collections;
        }
        return [];
    }

    private function getDefaultCollection()
    {
        if ($this->defaultCollection !== null) {
            return $this->defaultCollection;
        }
        return Collection::class;
    }

    protected function initCollections()
    {
        foreach ($this->collections as $key => $value) {
            $this->createCollection($key, $value);
        }
    }

    private function createCollection($key, $class)
    {
        if (is_int($key)) {
            $key   = $class;
            $class = $this->defaultCollection;
        }

        $this->{$key} = new $class();
        return $this->{$key};
    }
}
