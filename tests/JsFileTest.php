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
class JsFileTest extends PHPUnitAssets
{
    public function testCreateAssetLocalPathJS(): void
    {
        $asset = $this->factory->create('test', 'assets/js/jquery.js');
        isClass('JBZoo\Assets\Asset\JsFile', $asset);

        $result = $asset->load();
        isSame(AbstractAsset::TYPE_JS_FILE, $result[0]);
        isSamePath(PROJECT_TESTS . '/fixtures/assets/js/jquery.js', $result[1]);
    }

    public function testCreateAssetExternalPathJS(): void
    {
        $asset = $this->factory->create('test', 'https://site.com/script.js?v=42');
        isClass('JBZoo\Assets\Asset\JsFile', $asset);

        $result = $asset->load();
        isSamePath('https://site.com/script.js?v=42', $result[1]);
    }

    public function testCreateAssetVirtalPathJS(): void
    {
        $vpath = 'assets:js/script.js';

        $asset = $this->factory->create('test', $vpath);
        isClass('JBZoo\Assets\Asset\JsFile', $asset);

        $result = $asset->load();
        isTrue((bool)$result[1]);
        isSamePath($this->path->get($vpath), $result[1]);
    }

    public function testCreateUndefinedFile(): void
    {
        $vpath = 'assets:js/undefined.js';

        $asset = $this->factory->create('test', $vpath);
        isClass('JBZoo\Assets\Asset\JsFile', $asset);

        $result = $asset->load();
        isFalse((bool)$result[1]);
    }

    public function testCreateUndefinedFileStrictMode(): void
    {
        $this->expectException(\JBZoo\Assets\Exception::class);

        $this->manager->setParam('strict_mode', true);

        $vpath = 'assets:js/undefined.js';

        $asset = $this->factory->create('test', $vpath);
        $asset->load();
    }
}
