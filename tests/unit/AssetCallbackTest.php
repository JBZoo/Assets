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

namespace JBZoo\PHPUnit;

use JBZoo\Assets\Asset\Asset;

/**
 * Class AssetCallbackTest
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class AssetCallbackTest extends PHPUnitAssets
{
    public function testCreateAssetCallback()
    {
        $asset = $this->factory->create('test', function () {
        });

        isClass('JBZoo\Assets\Asset\Asset', $asset);
        isClass('JBZoo\Assets\Asset\Callback', $asset);
    }

    public function testLoadCallback()
    {
        $asset = $this->factory->create('test', function () {
            return 42;
        });

        $result = $asset->load();

        isSame(Asset::TYPE_CALLBACK, $result[0]);
        isSame(42, $result[1]);
    }

    public function testAddCallback()
    {
        $vaiable = 0;
        $this->manager->add('func', function () use (&$vaiable) {
            $vaiable++;
        });

        $this->manager->build();

        isSame(1, $vaiable);
    }

    public function testRegisterAndAddCallback()
    {
        $vaiable = 0;

        $this->manager->register('func', function () use (&$vaiable) {
            $vaiable++;
        });

        $this->manager->add('func');

        $this->manager->build();

        isSame(1, $vaiable);
    }

    public function testRegisterListOfCallback()
    {
        $vaiable = 1;

        $this->manager->add(
            'func',
            [
                function () use (&$vaiable) {
                    $vaiable += 10;
                },
                function () use (&$vaiable) {
                    $vaiable += 100;
                },
                function () use (&$vaiable) {
                    $vaiable /= 2;
                },
            ]
        );

        $this->manager->build();

        isSame(55.5, $vaiable);
    }
}
