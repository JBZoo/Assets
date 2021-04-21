<?php

/**
 * JBZoo Toolbox - Assets
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Assets
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Assets
 */

declare(strict_types=1);

namespace JBZoo\Assets\Asset;

use JBZoo\Assets\Manager;
use JBZoo\Data\Data;

use function JBZoo\Data\data;

/**
 * Class AbstractAsset
 * @package JBZoo\Assets\Asset
 */
abstract class AbstractAsset
{
    public const TYPE_JS_FILE    = 'js';
    public const TYPE_JS_CODE    = 'js_code';
    public const TYPE_JSX_FILE   = 'jsx';
    public const TYPE_JSX_CODE   = 'jsx_code';
    public const TYPE_CSS_FILE   = 'css';
    public const TYPE_CSS_CODE   = 'css_code';
    public const TYPE_LESS_FILE  = 'less';
    public const TYPE_CALLBACK   = 'callback';
    public const TYPE_COLLECTION = 'collection';

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var string|array
     */
    protected $source;

    /**
     * @var array
     */
    protected $dependencies = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var Manager
     */
    protected $eManager;

    /**
     * Asset constructor
     *
     * @param Manager      $manager
     * @param string       $alias
     * @param string|array $source
     * @param array        $dependencies
     * @param Data         $options
     */
    public function __construct(Manager $manager, $alias, $source, $dependencies, Data $options)
    {
        $this->eManager = $manager;
        $this->alias = $alias;
        $this->source = $source;
        $this->dependencies = (array)$dependencies;
        $this->options = (array)$options;
    }

    /**
     * Gets the name.
     *
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * Gets the dependencies.
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * @return string|array
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Gets the type.
     *
     * @return Data
     */
    public function getOptions(): Data
    {
        return data($this->options);
    }

    /**
     * @return array
     */
    abstract public function load(): array;
}
