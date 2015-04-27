<?php
 /**
 * Part of the Laradic packages.
 * MIT License and copyright information bundled with this package in the LICENSE file.
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
namespace Extensions\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Extension
 *
 * @package     Extensions\Models
 */
class Extension extends Model
{
    protected $fillable = ['slug', 'installed'];
}
