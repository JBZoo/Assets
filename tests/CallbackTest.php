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

namespace JBZoo\PHPUnit;

use JBZoo\Assets\Asset\AbstractAsset;

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class CallbackTest extends PHPUnitAssets
{
    public function testCreateAssetCallback(): void
    {
        $asset = $this->factory->create('test', static function (): void {
        });

        isClass('JBZoo\Assets\Asset\AbstractAsset', $asset);
        isClass('JBZoo\Assets\Asset\Callback', $asset);
    }

    public function testLoadCallback(): void
    {
        $asset = $this->factory->create('test', static fn () => 42);

        $result = $asset->load();

        isSame(AbstractAsset::TYPE_CALLBACK, $result[0]);
        isSame(42, $result[1]);
    }

    public function testAddCallback(): void
    {
        $vaiable = 0;
        $this->manager->add('func', static function () use (&$vaiable): void {
            $vaiable++;
        });

        $this->manager->build();

        isSame(1, $vaiable);
    }

    public function testRegisterAndAddCallback(): void
    {
        $vaiable = 0;

        $this->manager->register('func', static function () use (&$vaiable): void {
            $vaiable++;
        });

        $this->manager->add('func');

        $this->manager->build();

        isSame(1, $vaiable);
    }

    public function testRegisterListOfCallback(): void
    {
        $vaiable = 1;

        $this->manager->add(
            'func',
            [
                static function () use (&$vaiable): void {
                    $vaiable += 10;
                },
                static function () use (&$vaiable): void {
                    $vaiable += 100;
                },
                static function () use (&$vaiable): void {
                    $vaiable /= 2;
                },
            ],
        );

        $this->manager->build();

        isSame(55.5, $vaiable);
    }
}
