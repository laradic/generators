<?php /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */

namespace Laradic\Generators\Toolbox;

class ToolboxFile
{
    protected $registrar;
    protected $providers;

    public function registrar()
    {
        return $this->registrar ?: $this->registrar = new Registrar();
    }

    public function asdf()
    {
        $registrar = $this->registrar();
        $registrar
            ->signature(\Illuminate\Contracts\Config\Repository::class . '::get:1')
            ->signature(\Illuminate\Config\Repository::class . '::get:1')
            ->signature('config')
            ->signature('config:1')
            ->signature(\Illuminate\Contracts\Config\Repository::class . '::set:1')
            ->signature(\Illuminate\Config\Repository::class . '::set:1');
        $signature = $registrar->signatures();
        $signature->set([
            'type'   => 'type',
            'class'  => \Illuminate\Contracts\Config\Repository::class,
            'method' => 'get',
        ]);
        $signature->type('type')
            ->class(\Illuminate\Contracts\Config\Repository::class)
            ->method('get');
    }
}