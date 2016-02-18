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
 */

namespace JBZoo\PHPUnit;

use JBZoo\Assets\Package;
use JBZoo\Assets\Exception;

/**
 * Class PackageTest
 * @package JBZoo\PHPUnit
 */
class PackageTest extends PHPUnit
{

    public function testShouldDoSomeStreetMagic()
    {
        $obj = new Package();

        is('street magic', $obj->doSomeStreetMagic());
    }

    /**
     * @expectedException \JBZoo\Assets\Exception
     */
    public function testShouldShowException()
    {
        throw new Exception('Test message');
    }
}
