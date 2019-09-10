<?php

namespace Laradic\Tests\Generators;

use ReflectionClass;
use Illuminate\Database\Eloquent\Model;
use Laradic\Generators\Base\Model\ClassModel;
use Laradic\Generators\Base\Model\NamespaceModel;
use Laradic\Generators\Core\Elements\ClassElement;
use Laradic\Generators\Core\Converters\ReflectionClassConverter;

class FactoryTest extends TestCase
{
    public function testBase()
    {
        $ref   = new ReflectionClass(Model::class);
        $class = new ClassModel();
        $class->setNamespace(new NamespaceModel('sf'));

        $this->assertTrue(true);
    }

    public function testCore()
    {
        $ref   = new ReflectionClass(Model::class);
        $converted = (new ReflectionClassConverter($ref))->convert();
        $render = $converted->render();
        file_put_contents(__DIR__.'/../generated.model.php', $render);

        $class = new ClassElement();
        $class->setName('MyClass', null, [ 'ArrayAccess' ]);
        $class->addUse('ArrayAccess');
        $class->setNamespace('MyNS');
        $class->addMethod('get', 'public')
            ->addArgument('key', 'string')
            ->addArgument('default', null, 'null');
        $class->addProperty('items', 'protected', '[]')
            ->setDocBlock('@var array');

        $render = $class->render();
        file_put_contents(__DIR__.'/../generated.myclass.php', $render);
        $this->assertTrue(true);
    }
}
