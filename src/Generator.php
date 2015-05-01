<?php
/**
 * Part of the Laradic packages.
 * MIT License and copyright information bundled with this package in the LICENSE file.
 *
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
namespace Laradic\Generators;

use Laradic\Support\Filesystem;
use Illuminate\Contracts\View\Factory as View;
use Laradic\Support\String;
use Laradic\Support\Traits\DotArrayAccess;

/**
 * Class Generator
 *
 * @package     Laradic\Generators
 */
class Generator
{
    use DotArrayAccess;

    protected function getArrayAccessor()
    {
        return 'attributes';
    }

    protected $attributes = [];

    /** @var \Laradic\Support\Filesystem */
    protected $fs;

    /**
     * @var \Illuminate\View\Factory
     */
    protected $view;

    /**
     * Absolute path to the source "stubs" directory
     * @var
     */
    protected $from;

    /**
     * Absolute path to the destination directory
     * @var string
     */
    protected $to;

    /** Instantiates the class
     *
     * @param \Laradic\Support\Filesystem        $files
     * @param \Illuminate\Contracts\View\Factory $view
     */
    public function __construct(Filesystem $fs, View $view)
    {
        $this->fs = $fs;
        $this->view  = $view;
    }

    public function generate(array $files, array $values = [])
    {
        foreach ( $files as $src => $fileName )
        {
            $segments    = explode('/', $src);
            $srcFileName = last($segments);
            array_pop($segments);
            $srcDir = implode('/', $segments);


            $destinationDir = path_join($this->to, $srcDir);
            if ( $fileName === false )
            {
                $fileName = String::remove($srcFileName, '.stub');
            }
            $destinationPath = path_join($destinationDir, $fileName);

            $src = path_join($this->from, $src);

            if ( ! $this->fs->isDirectory($destinationDir) )
            {
                $this->mkdir($destinationDir);
            }

            $content = $this->render($src, array_replace_recursive($this->attributes, $values));

            $this->fs->put($destinationPath, $content);
        }
    }

    public function render($filePath, array $values = [])
    {
        return $this->view
            ->file($filePath)
            ->with(array_replace_recursive($this->attributes, $values))
            ->render();
    }

    protected function mkdir()
    {
        $this->fs->makeDirectory(path_join(func_get_args()), 0755, true);
        return $this;
    }

    public function set($key, $value = null)
    {
        $this->offsetSet($key, $value);
        return $this;
    }

    /**
     * get to value
     *
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set the to value
     *
     * @param string $to
     * @return Generator
     */
    public function to($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * get from value
     *
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set the from value
     *
     * @param mixed $from
     * @return Generator
     */
    public function from($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * get attributes value
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }



}
