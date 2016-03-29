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

namespace JBZoo\Assets\Asset;

use JBZoo\Assets\Manager;
use JBZoo\Data\Data;
use JBZoo\Path\Path;

/**
 * Class Asset
 * @package JBZoo\Assets\Asset
 */
abstract class Asset
{
    const TYPE_JS_FILE    = 'js';
    const TYPE_JS_CODE    = 'js_code';
    const TYPE_JSX_FILE   = 'jsx';
    const TYPE_JSX_CODE   = 'jsx_code';
    const TYPE_CSS_FILE   = 'css';
    const TYPE_CSS_CODE   = 'css_code';
    const TYPE_LESS_FILE  = 'less';
    const TYPE_CALLBACK   = 'callback';
    const TYPE_COLLECTION = 'collection';

    /**
     * @var string
     */
    protected $_alias;

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
     * @var Path
     */
    protected $_path;

    /**
     * @var Data
     */
    protected $_params;

    /**
     * @var Manager
     */
    protected $_manager;

    /**
     * Asset constructor
     *
     * @param Manager $manager
     * @param string  $alias
     * @param mixed   $source
     * @param array   $dependencies
     * @param Data    $options
     */
    public function __construct(Manager $manager, $alias, $source, $dependencies, Data $options)
    {
        $this->_manager      = $manager;
        $this->_alias        = $alias;
        $this->_source       = $source;
        $this->_dependencies = (array)$dependencies;
        $this->_options      = $options;
    }

    /**
     * Gets the name.
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->_alias;
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
     * @return Data
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * @param array $filters
     * @return array
     */
    abstract public function load(array $filters = []);
}
