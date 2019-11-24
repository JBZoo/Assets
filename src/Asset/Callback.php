<?php
/**
 * JBZoo Assets
 *
 * This file is part of the JBZoo CCK package.
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
class Callback extends Asset
{
    /**
     * @var callable
     */
    protected $source;

    /**
     * {@inheritdoc}
     */
    public function load(array $filters = [])
    {
        $result = call_user_func($this->source, $this, $this->params, $filters);

        return [Asset::TYPE_CALLBACK, $result];
    }
}
