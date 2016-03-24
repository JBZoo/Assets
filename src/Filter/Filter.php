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

use JBZoo\Assets\Asset\Asset;

/**
 * Class Filter
 *
 * @package JBZoo\Assets\Filter
 */
abstract class Filter
{
    /**
     * @var Asset
     */
    protected $_asset;

    /**
     * @param Asset $asset
     * @return $this
     */
    public function setAsset(Asset $asset)
    {
        $this->_asset = $asset;
        return $this;
    }

    /**
     * @return mixed
     */
    abstract public function process();
}
