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

namespace JBZoo\Assets\Asset;

/**
 * Class Callback
 * @package JBZoo\Assets\Asset
 */
class Callback extends AbstractAsset
{
    /**
     * @var callable
     */
    protected $source;

    /**
     * @inheritDoc
     */
    public function load()
    {
        $result = call_user_func($this->source, $this);

        return [AbstractAsset::TYPE_CALLBACK, $result];
    }
}
