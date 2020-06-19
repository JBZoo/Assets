<?php

/**
 * JBZoo Toolbox - Assets
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Assets
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Assets
 */

namespace JBZoo\PHPUnit;

use JBZoo\Assets\Asset\AbstractAsset;
use JBZoo\Assets\Asset\AbstractFile;

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
                AbstractAsset::TYPE_JS_FILE  => [],
                AbstractAsset::TYPE_JS_CODE  => [],
                AbstractAsset::TYPE_JSX_FILE => [],
                AbstractAsset::TYPE_JSX_CODE => [],
                AbstractAsset::TYPE_CSS_FILE => [],
                AbstractAsset::TYPE_CSS_CODE => [],
                AbstractAsset::TYPE_CALLBACK => [],
            ],
            $this->manager->build()
        );
    }

    public function testRegisterNewAssets()
    {
        $this->manager->register('bootstrap', 'assets/css/libs/bootstrap.css');
        $collection = $this->manager->getCollection();

        isClass('\JBZoo\Assets\Asset\CssFile', $collection->get('bootstrap'));
        isSame('bootstrap', $collection->get('bootstrap')->getAlias());
        isSame(1, $collection->count());
    }

    public function testRegisterLocalAssets()
    {
        $this->manager
            ->add('custom', 'assets/css/custom.css')
            ->register('bootstrap', 'assets/css/libs/bootstrap.css')
            ->add('bootstrap');
        $collection = $this->manager->getCollection();

        /** @var AbstractFile $asset */
        $asset = $collection->get('bootstrap');

        isSame('bootstrap', $asset->getAlias());
        isSame(2, $collection->count());
    }

    public function testUnRegisterAssets()
    {
        $this->manager
            ->add('custom', 'assets/css/custom.css')
            ->register('styles', 'assets/css/styles.css')
            ->register('template', 'assets/css/template.css')
            ->register('bootstrap', 'assets/css/libs/bootstrap.css');

        $collection = $this->manager->getCollection();

        isSame(4, $collection->count());

        $this->manager->unregister('styles');
        isSame(3, $collection->count());
        isNull($collection->get('styles'));
    }

}
