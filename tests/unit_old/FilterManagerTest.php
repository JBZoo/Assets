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

use JBZoo\Assets\Filter\FilterManager;

/**
 * Class FilterManagerTest
 * @package JBZoo\PHPUnit
 */
class FilterManagerTest_old extends PHPUnit
{
    public function testGetFilter()
    {
        $filters = new FilterManager();
        isClass('JBZoo\Assets\Filter\CssCompressor', $filters->get('CssCompressor'));
    }

    public function testGetCustomFilter()
    {
        $filters = new FilterManager();
        $filters->add('CustomFilter', 'Custom\Assets\CustomFilter');
        isClass('Custom\Assets\CustomFilter', $filters->get('CustomFilter'));
    }

    public function testGetNoExist()
    {
        $filters = new FilterManager();
        $filters->get('CustomFilter');
    }
}
