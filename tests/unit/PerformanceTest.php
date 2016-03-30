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

namespace JBZoo\PHPUnit;

use JBZoo\Path\Path;
use JBZoo\Assets\Collection;
use JBZoo\Assets\Factory;
use JBZoo\Assets\Manager;
use JBZoo\Profiler\Benchmark;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class PerformanceTest
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class PerformanceTest extends PHPUnitAssets
{

    public function testSpeedUniq()
    {

        $index = 0;

        Benchmark::compare(
            [
                'register (i++)' => function () use (&$index) {
                    $this->_manager->register('item-' . ++$index, 'assets:less/styles.less');
                },

                'add (i++)' => function () use (&$index) {
                    $this->_manager->register('item-' . ++$index, 'assets:less/styles.less');
                },
            ],
            ['count' => 1000]
        );
    }

    public function testSpeedSame()
    {
        Benchmark::compare(
            [
                'register' => function () use (&$index) {
                    $this->_manager->register('item', 'assets:less/styles.less');
                },

                'add' => function () {
                    $this->_manager->add('item', 'assets:less/styles.less');
                },
            ],
            ['count' => 1000]
        );
    }
}
