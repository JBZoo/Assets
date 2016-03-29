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
 * Class AssetJsxFileTest
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class AssetJsxFileTest extends PHPUnitAssets
{
    public function testCreateAssetLocalPathJSX()
    {
        $asset = $this->_factory->create('test', 'assets/jsx/react-component.jsx');
        isClass('JBZoo\Assets\Asset\JsxFile', $asset);

        $result = $asset->load();
        isSame(Asset::TYPE_JSX_FILE, $result[0]);
        isSamePath(PROJECT_TESTS . '/fixtures/assets/jsx/react-component.jsx', $result[1]);
    }
}
