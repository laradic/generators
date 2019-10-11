<?php

namespace Laradic\Generators\Stub;

use Laradic\Support\Wrap;
use Illuminate\View\Factory;
use Illuminate\Filesystem\Filesystem;

class StubGenerator
{
    /** @var \Illuminate\Filesystem\Filesystem */
    protected $fs;

    /** @var \Illuminate\View\Factory */
    protected $factory;

    /** @var string */
    protected $cwd;

    public function __construct(Filesystem $fs, Factory $factory)
    {
        $this->fs      = $fs;
        $this->factory = $factory;
        $this->cd(getcwd());
    }

    /**
     * Prepend a namespace to the finder
     *
     * @param string       $namespace
     * @param string|array $hints
     * @return $this
     */
    public function addNamespace($namespace, $hints)
    {
        $this->factory->prependNamespace($namespace, $hints);
        return $this;
    }

    public function render($name, $data = [], $mergeData = [])
    {
        $view    = $this->factory->make($name, $data, $mergeData);
        $renderd = $view->render();
        return $renderd;
    }

    public function generate($filePath, $name, $data = [])
    {
        $filePath = $this->path($filePath);
        $this->ensureDirectory(dirname($filePath));
        $D        = Wrap::dot()->referenced($data);
        $rendered = $this->render($name, $data, [ '_' => $D ]);
        $this->fs->put($filePath, $rendered);
        return $filePath;
    }

    public function cd($cwd)
    {
        if (path_is_relative($cwd)) {
            $cwd = base_path($cwd);
        }
        $this->cwd = $cwd;
        return $this;
    }

    protected function path($path)
    {
        if (path_is_relative($path)) {
            return path_join($this->cwd, $path);
        }
        return $path;
    }

    protected function ensureDirectory($dir)
    {
        if ( ! $this->fs->isDirectory($dir)) {
            $this->fs->makeDirectory($dir, 0755, true);
        }
    }

    public function getFactory()
    {
        return $this->factory;
    }

    public function getCwd()
    {
        return $this->cwd;
    }


}