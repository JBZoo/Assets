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

namespace Custom\Assets;

use JBZoo\Utils\FS;
use JBZoo\Assets\Asset;
use JBZoo\Assets\FileAsset;
use JBZoo\Assets\Filter\FilterAbstract;
use JBZoo\Assets\Filter\FilterManager;

/**
 * Class CustomAsset
 *
 * @package Custom\Assets
 */
class CustomAsset extends Asset
{

    public function load(array $filters = [])
    {
        $assetExt = FS::clean($this->_root . '/' . $this->_source, '/');

        if (!FileAsset::isExternal($this->_source)) {
            $fManager = new FilterManager();

            $path = $this->_source;
            if (count($filters)) {
                foreach ($filters as $name) {
                    /** @var FilterAbstract $filter */
                    $filter  = $fManager->get($name);
                    $filter->setAsset($this);
                    $path = $filter->process();
                }
            }

            return [$assetExt, $path];
        }

        return [$assetExt, $this->_source];
    }
}
