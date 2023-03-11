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
use JBZoo\Assets\Manager;
use JBZoo\Path\Path;
use JBZoo\Utils\FS;

/**
 * Class FactoryTest
 * @package JBZoo\PHPUnit
 */
class FactoryTest extends PHPUnitAssets
{
    public function testDefaultParams()
    {
        $params = $this->manager->getParams();

        isClass('JBZoo\Data\Data', $params);
        isFalse((bool)$params->get('debug'));
        isSame([], $params->get('less'));

        isSame([
            'debug'       => false,
            'strict_mode' => false,
            'less'        => [],
        ], $params->getArrayCopy());
    }

    public function testReloadParams()
    {
        $manager = new Manager(new Path(), [
            'debug'       => true,
            'some_option' => 123456,
        ]);

        $params = $manager->getParams();
        isTrue((bool)$params->get('debug'));
        isSame(123456, $params->get('some_option'));
    }

    public function testCreateUndefinedAssetType()
    {
        $this->expectException(\JBZoo\Assets\Exception::class);

        $this->factory->create('test', 'path/to/my-file.undefined');
    }

    public function testCreateDifferentTypes()
    {
        // CSS File
        $asset = $this->factory->create('test', 'file.css');
        isClass('JBZoo\Assets\Asset\AbstractAsset', $asset);
        isClass('JBZoo\Assets\Asset\AbstractFile', $asset);
        isClass('JBZoo\Assets\Asset\CssFile', $asset);

        // JS File
        $asset = $this->factory->create('test', 'http://site.com/script.js?version=1');
        isClass('JBZoo\Assets\Asset\AbstractAsset', $asset);
        isClass('JBZoo\Assets\Asset\AbstractFile', $asset);
        isClass('JBZoo\Assets\Asset\JsFile', $asset);

        // Less File
        $asset = $this->factory->create('test', 'file.less');
        isClass('JBZoo\Assets\Asset\AbstractAsset', $asset);
        isClass('JBZoo\Assets\Asset\AbstractFile', $asset);
        isClass('JBZoo\Assets\Asset\LessFile', $asset);

        // JS Custom Code
        $asset = $this->factory->create('test', 'alert(1);', [], ['type' => AbstractAsset::TYPE_JS_CODE]);
        isClass('JBZoo\Assets\Asset\AbstractAsset', $asset);
        isClass('JBZoo\Assets\Asset\JsCode', $asset);

        // CSS Custom Code
        $asset = $this->factory->create('test', 'div{display:block;}', [], ['type' => AbstractAsset::TYPE_CSS_CODE]);
        isClass('JBZoo\Assets\Asset\AbstractAsset', $asset);
        isClass('JBZoo\Assets\Asset\CssCode', $asset);
    }

    public function testCreateSimpleAssets()
    {
        $asset = $this->factory->create('test', 'path/to/my-file.css');

        isSame('test', $asset->getAlias());
        isSame('path/to/my-file.css', $asset->getSource());
        isSame([], $asset->getDependencies());
        isSame([], $asset->getOptions()->getArrayCopy());
    }


    public function testCreateByDependenciesIsString()
    {
        $asset = $this->factory->create('test', '\path\to/my-file.JS', 'uikit');

        isSame('test', $asset->getAlias());
        isSame('\path\to/my-file.JS', $asset->getSource());
        isSame(['uikit'], $asset->getDependencies());
        isSame([], $asset->getOptions()->getArrayCopy());
    }

    public function testCreateByDependenciesIsArray()
    {
        $asset = $this->factory->create('test', '\path\to/my-file.css', ['uikit', 'jquery-ui']);

        isSame('test', $asset->getAlias());
        isSame('\path\to/my-file.css', $asset->getSource());
        isSame(['uikit', 'jquery-ui'], $asset->getDependencies());
        isSame([], $asset->getOptions()->getArrayCopy());
    }

    public function testGetRoot()
    {
        $dir = __DIR__;

        $path = new Path();
        $path->setRoot($dir);
        $manager = new Manager($path);

        isSame(
            FS::clean($dir),
            FS::clean($manager->getPath()->getRoot())
        );
    }
}
