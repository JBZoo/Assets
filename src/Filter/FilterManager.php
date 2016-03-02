<?php
/**
 * JBZoo Assets
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   Assets
 * @license   MIT
 * @copyright Copyright (C) JBZoo.com,  All rights reserved.
 * @link      https://github.com/JBZoo/Assets
 * @author    Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */

namespace JBZoo\Assets\Filter;

use JBZoo\Data\Data;
use JBZoo\Utils\Arr;

/**
 * Class FilterManager
 *
 * @package JBZoo\Assets\Filter
 */
class FilterManager
{

    /**
     * Filter map.
     *
     * @var array
     */
    protected $_filters = [
        'CssCompressor' => 'JBZoo\Assets\Filter\CssCompressor',
    ];

    /**
     * Get filter by name.
     *
     * @param $name
     * @return bool|FilterAbstract
     */
    public function get($name)
    {
        if (Arr::key($name, $this->_filters)) {
            return new $this->_filters[$name]();
        }

        return false;
    }

    /**
     * Add new filter.
     *
     * @param string $name
     * @param string $class
     * @return $this
     */
    public function add($name, $class)
    {
        $this->_filters[$name] = $class;
        return $this;
    }
}
