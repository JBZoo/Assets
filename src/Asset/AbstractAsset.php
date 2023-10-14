<?php

/**
 * JBZoo Toolbox - Assets.
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @see        https://github.com/JBZoo/Assets
 */

declare(strict_types=1);

namespace JBZoo\Assets\Asset;

use JBZoo\Assets\Manager;
use JBZoo\Data\Data;

use function JBZoo\Data\data;

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

    protected string                $alias;
    protected array|\Closure|string $source;
    protected array                 $dependencies = [];
    protected array                 $options      = [];
    protected Manager               $eManager;

    abstract public function load(): array;

    public function __construct(
        Manager $manager,
        string $alias,
        array|\Closure|string $source,
        array|string $dependencies,
        Data $options,
    ) {
        $this->eManager     = $manager;
        $this->alias        = $alias;
        $this->source       = $source;
        $this->dependencies = (array)$dependencies;
        $this->options      = $options->getArrayCopy();
    }

    /**
     * Gets the name.
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * Gets the dependencies.
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function getSource(): array|\Closure|string
    {
        return $this->source;
    }

    /**
     * Gets the type.
     */
    public function getOptions(): Data
    {
        return data($this->options);
    }
}
