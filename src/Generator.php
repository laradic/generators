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
use Laradic\Support\TemplateParser;

/**
 * Class Generator
 *
 * @package     Laradic\Generators
 */
class Generator
{
    /** @var \Laradic\Support\TemplateParser  */
    protected $parser;

    /** @var \Laradic\Support\Filesystem  */
    protected $files;

    protected $destinationDirPath;

    /**
     * Instantiates the class
     *
     * @param \Laradic\Support\Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files  = $files;
        $this->parser = new TemplateParser($files, $this->getStubsPath());
    }

    public function create($stubFile, $destination, array $values = array())
    {
        $this->parser->copy($stubFile, $destination, $values);
    }

    public function getStubsPath()
    {
        return realpath(path_join(__DIR__, 'resources/stubs'));
    }

    /**
     * Get the value of destinationDirPath
     *
     * @return mixed
     */
    public function getDestinationDirPath()
    {
        return $this->destinationDirPath;
    }

    /**
     * Sets the value of destinationDirPath
     *
     * @param mixed $destinationDirPath
     * @return mixed
     */
    public function setDestinationDirPath($destinationDirPath)
    {
        $this->destinationDirPath = $destinationDirPath;

        return $this;
    }

    /**
     * Get the value of parser
     *
     * @return TemplateParser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Sets the value of parser
     *
     * @param TemplateParser $parser
     * @return TemplateParser
     */
    public function setParser($parser)
    {
        $this->parser = $parser;

        return $this;
    }




}
