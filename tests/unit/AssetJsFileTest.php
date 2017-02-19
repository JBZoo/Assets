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

/**
 * Class AssetJsFileTest
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class AssetJsFileTest extends PHPUnitAssets
{
    public function testCreateAssetLocalPathJS()
    {
        $asset = $this->_factory->create('test', 'assets/js/jquery.js');
        isClass('JBZoo\Assets\Asset\JsFile', $asset);

        $result = $asset->load();
        isSame(Asset::TYPE_JS_FILE, $result[0]);
        isSamePath(PROJECT_TESTS . '/fixtures/assets/js/jquery.js', $result[1]);
    }

    public function testCreateAssetExternalPathJS()
    {
        $asset = $this->_factory->create('test', 'https://site.com/script.js?v=42');
        isClass('JBZoo\Assets\Asset\JsFile', $asset);

        $result = $asset->load();
        isSamePath('https://site.com/script.js?v=42', $result[1]);
    }

    public function testCreateAssetVirtalPathJS()
    {
        $vpath = 'assets:js/script.js';

        $asset = $this->_factory->create('test', $vpath);
        isClass('JBZoo\Assets\Asset\JsFile', $asset);

        $result = $asset->load();
        isTrue($result[1]);
        isSamePath($this->_path->get($vpath), $result[1]);
    }

    public function testCreateUndefinedFile()
    {
        $vpath = 'assets:js/undefined.js';

        $asset = $this->_factory->create('test', $vpath);
        isClass('JBZoo\Assets\Asset\JsFile', $asset);

        $result = $asset->load();
        isFalse($result[1]);
    }

    /**
     * @expectedException \JBZoo\Assets\Exception
     */
    public function testCreateUndefinedFileStrictMode()
    {
        $this->_manager->setParam('strict_mode', true);

        $vpath = 'assets:js/undefined.js';

        $asset = $this->_factory->create('test', $vpath);
        $asset->load();
    }
}
