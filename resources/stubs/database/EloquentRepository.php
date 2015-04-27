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
namespace Extensions\Repositories;

use Extensions\Contracts\ExtensionRepository;
use Illuminate\Database\Eloquent\Model;
use Laradic\Support\AbstractEloquentRepository;

/**
 * Class EloquentExtensionRepository
 *
 * @package     Extensions\Repositories
 */
class EloquentExtensionRepository extends AbstractEloquentRepository implements ExtensionRepository
{
    /** @var \Illuminate\Database\Eloquent\Model  */
    protected $model;

    /**
     * Instantiates the class
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get extension by usint the slug
     *
     * @param $slug
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function getBySlug($slug)
    {
        return $this->getFirstBy('slug', $slug);
    }

    /**
     * Get installed extensions
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getInstalled()
    {
        return $this->getManyBy('installed', 1);
    }

    /**
     * Get uninstalled extensions
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getUninstalled()
    {
        return $this->getManyBy('installed', 0);
    }
}
