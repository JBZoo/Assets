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
class JsxFileTest extends PHPUnitAssets
{
    public function testCreateAssetLocalPathJSX(): void
    {
        $asset = $this->factory->create('test', 'assets/jsx/react-component.jsx');
        isClass('JBZoo\Assets\Asset\JsxFile', $asset);

        $result = $asset->load();
        isSame(AbstractAsset::TYPE_JSX_FILE, $result[0]);
        isSamePath(PROJECT_TESTS . '/fixtures/assets/jsx/react-component.jsx', $result[1]);
    }
}
