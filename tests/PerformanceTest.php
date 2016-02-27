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

/**
 * Class PerformanceTest
 *
 * @package JBZoo\PHPUnit
 */
class PerformanceTest extends PHPUnit
{
    protected $_max = 1000000;

    public function testLeakMemoryCreate()
    {
        if ($this->isXDebug()) {
            return;
        }
    }
}
