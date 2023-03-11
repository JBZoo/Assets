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
 * Class AssetCssFileTest
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class CssFileTest extends PHPUnitAssets
{
    public function testCreateAssetLocalPathCSS()
    {
        $asset = $this->factory->create('test', 'assets/css/styles.css');
        isClass('JBZoo\Assets\Asset\CssFile', $asset);

        $result = $asset->load();
        isSame(AbstractAsset::TYPE_CSS_FILE, $result[0]);
        isSamePath(PROJECT_TESTS . '/fixtures/assets/css/styles.css', $result[1]);
    }

    public function testCreateAssetExternalPathCSS()
    {
        $asset = $this->factory->create('test', 'https://site.com/styles.css?v=4242&var2=qwerty');
        isClass('JBZoo\Assets\Asset\CssFile', $asset);

        $result = $asset->load();
        isSamePath('https://site.com/styles.css?v=4242&var2=qwerty', $result[1]);
    }

    public function testCreateAssetVirtalPathCSS()
    {
        $vpath = 'assets:css/styles.css';

        $asset = $this->factory->create('test', $vpath);
        isClass('JBZoo\Assets\Asset\CssFile', $asset);

        $result = $asset->load();
        isTrue((bool)$result[1]);
        isSamePath($this->path->get($vpath), $result[1]);
    }

    public function testCreateUndefinedFile()
    {
        $vpath = 'assets:css/undefined.css';

        $asset = $this->factory->create('test', $vpath);
        isClass('JBZoo\Assets\Asset\CssFile', $asset);

        $result = $asset->load();
        isFalse((bool)$result[1]);
    }
}
