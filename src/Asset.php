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

namespace JBZoo\Assets;

use JBZoo\Data\Data;

/**
 * Class Asset
 *
 * @package JBZoo\Assets
 */
abstract class Asset implements AssetInterface
{

    /**
     * @var string
     */
    protected $_name;

    /**
     * @var string
     */
    protected $_source;

    /**
     * @var array
     */
    protected $_dependencies = [];

    /**
     * @var array
     */
    protected $_options = [];

    /**
     * @var string
     */
    protected $_root;

    /**
     * @var Data
     */
    protected $_params;

    /**
     * AssetAbstract constructor.
     *
     * @param string       $name
     * @param Data         $params
     * @param string       $source
     * @param string|array $dependencies
     * @param string|array $options
     * @param string       $root
     */
    public function __construct($root, Data $params, $name, $source, $dependencies = [], $options = [])
    {
        $this->_name         = $name;
        $this->_root         = $root;
        $this->_source       = $source;
        $this->_params       = $params;
        $this->_options      = (array)$options;
        $this->_dependencies = (array)$dependencies;
    }

    /**
     * @return Data
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Gets the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Gets the dependencies.
     *
     * @return array
     */
    public function getDependencies()
    {
        return $this->_dependencies;
    }

    /**
     * Gets the source.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * Gets the type.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * @param array $filters
     * @return mixed
     */
    abstract public function load(array $filters = []);
}
