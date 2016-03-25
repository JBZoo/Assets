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

use JBZoo\Assets\Asset\Asset;
use JBZoo\Assets\Asset\File;

/**
 * Class ManagerTest
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class ManagerTest extends PHPUnitAssets
{
    public function testEmptyBuild()
    {
        isSame(
            [
                Asset::TYPE_JS_FILE  => [],
                Asset::TYPE_CSS_FILE => [],
                Asset::TYPE_JS_CODE  => [],
                Asset::TYPE_CSS_CODE => [],
            ],
            $this->_manager->build()
        );
    }

    public function testRegisterNewAssets()
    {
        $this->_manager->register('bootstrap', 'assets/css/libs/bootstrap.css');
        $collection = $this->_manager->getCollection();

        isClass('\JBZoo\Assets\Asset\CssFile', $collection->get('bootstrap'));
        isSame('bootstrap', $collection->get('bootstrap')->getAlias());
        isSame(1, $collection->count());
    }

    public function testRegisterLocalAssets()
    {
        $this->_manager
            ->add('custom', 'assets/css/custom.css')
            ->register('bootstrap', 'assets/css/libs/bootstrap.css')
            ->add('bootstrap');
        $collection = $this->_manager->getCollection();

        /** @var File $asset */
        $asset = $collection->get('bootstrap');

        isSame('bootstrap', $asset->getAlias());
        isSame(2, $collection->count());
    }

    public function testUnRegisterAssets()
    {
        $this->_manager
            ->add('custom', 'assets/css/custom.css')
            ->register('styles', 'assets/css/styles.css')
            ->register('template', 'assets/css/template.css')
            ->register('bootstrap', 'assets/css/libs/bootstrap.css');

        $collection = $this->_manager->getCollection();

        isSame(4, $collection->count());

        $this->_manager->unregister('styles');
        isSame(3, $collection->count());
        isNull($collection->get('styles'));
    }

}
