<?php

namespace Laradic\Generators\Toolbox;

use ArrayAccess;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Support\Arrayable;
use Laradic\Support\Traits\ArrayableProperties;
use Laradic\Support\Traits\ArrayAccessibleProperties;

class RegistrarSignature implements Arrayable, ArrayAccess
{
    use ArrayAccessibleProperties;
    use ArrayableProperties {
        toArray as _toArray;
    }

    protected $function;
    protected $type;
    protected $class;
    protected $field;
    protected $array;
    protected $index;
    protected $method;

    protected function getArrayableProperties()
    {
        return [ 'function', 'type', 'class', 'field', 'array', 'index', 'method' ];
    }

    public function clone()
    {
        $clone = new static();
        foreach ($this->getArrayableProperties() as $field) {
            $clone[ $field ] = $this[ $field ];
        }
        return $clone;
    }

    public function reset()
    {
        foreach ($this->getArrayableProperties() as $field) {
            $clone[ $field ] = null;
        }
        return $this;
    }

    /**
     * @param array $data = array(
     *     'function' => '',
     *     'type' => 'array_key',
     *     'type' => 'annotation_key',
     *     'type' => 'annotation',
     *     'type' => 'type',
     *     'type' => 'return',
     *     'type' => 'default',
     *     'class' => '',
     *     'field' => '',
     *     'array' => '',
     *     'index' => 0,
     *     'method' => '',
     * )
     * @return $this
     */
    public function set($data)
    {
        foreach (Arr::only($data, $this->getArrayableProperties()) as $field => $value) {
            $this[ $field ] = $value;
        }
        return $this;
    }

    public function function ()
    {
        if (func_num_args() === 0) {
            return $this->function;
        }
        $this->function = func_get_args()[ 0 ];
        return $this;
    }

    public function type()
    {
        if (func_num_args() === 0) {
            return $this->type;
        }
        $this->type = func_get_args()[ 0 ];
        return $this;
    }

    public function class()
    {
        if (func_num_args() === 0) {
            return $this->class;
        }
        $this->class = func_get_args()[ 0 ];
        return $this;
    }

    public function field()
    {
        if (func_num_args() === 0) {
            return $this->field;
        }
        $this->field = func_get_args()[ 0 ];
        return $this;
    }

    public function array()
    {
        if (func_num_args() === 0) {
            return $this->array;
        }
        $this->array = func_get_args()[ 0 ];
        return $this;
    }

    public function index()
    {
        if (func_num_args() === 0) {
            return $this->index;
        }
        $this->index = func_get_args()[ 0 ];
        return $this;
    }

    public function method()
    {
        if (func_num_args() === 0) {
            return $this->method;
        }
        $this->method = func_get_args()[ 0 ];
        return $this;
    }

    public function toArray()
    {
        array_filter($this->_toArray(), function($val){
            return $val !== null;
        });
    }


//    public function getFunction()
//    {
//        return $this->function;
//    }
//
//    public function setFunction($function)
//    {
//        $this->function = $function;
//        return $this;
//    }
//
//    public function getType()
//    {
//        return $this->type;
//    }
//
//    public function setType($type)
//    {
//        $this->type = $type;
//        return $this;
//    }
//
//    public function getClass()
//    {
//        return $this->class;
//    }
//
//    public function setClass($class)
//    {
//        $this->class = $class;
//        return $this;
//    }
//
//    public function getField()
//    {
//        return $this->field;
//    }
//
//    public function setField($field)
//    {
//        $this->field = $field;
//        return $this;
//    }
//
//    public function getArray()
//    {
//        return $this->array;
//    }
//
//    public function setArray($array)
//    {
//        $this->array = $array;
//        return $this;
//    }
//
//    public function getIndex()
//    {
//        return $this->index;
//    }
//
//    public function setIndex($index)
//    {
//        $this->index = $index;
//        return $this;
//    }
//
//    public function getMethod()
//    {
//        return $this->method;
//    }
//
//    public function setMethod($method)
//    {
//        $this->method = $method;
//        return $this;
//    }


}