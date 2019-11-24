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

use JBZoo\Profiler\Benchmark;

/**
 * Class AssetsPerformanceTest
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class AssetsPerformanceTest extends PHPUnitAssets
{

    public function testSpeedUniq()
    {
        $index = 0;

        Benchmark::compare([
            'register (i++)' => function () use (&$index) {
                $this->manager->register('item-' . ++$index, 'assets:less/styles.less');
            },

            'add (i++)' => function () use (&$index) {
                $this->manager->register('item-' . ++$index, 'assets:less/styles.less');
            },
        ], ['count' => 1000]);

        isTrue(true);
    }

    public function testSpeedSame()
    {
        Benchmark::compare([
            'register' => function () use (&$index) {
                $this->manager->register('item', 'assets:less/styles.less');
            },

            'add' => function () {
                $this->manager->add('item', 'assets:less/styles.less');
            },
        ], ['count' => 1000]);
        isTrue(true);
    }
}
