<?php

namespace Laradic\Generators\Doc\Doc;

use Laradic\Generators\Doc\DocBlock;

interface Doc
{
    /** @return DocBlock */
    public function getDocblock();

    /** @return \ReflectionClass|\ReflectionProperty|\ReflectionMethod */
    public function getReflection();

    /** @return ClassDoc */
    public function getClassDoc();
}
