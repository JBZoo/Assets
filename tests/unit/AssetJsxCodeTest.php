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
 * Class AssetJsxCodeTest
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class AssetJsxCodeTest extends PHPUnitAssets
{
    public function testCreateAssetJsCode()
    {
        $jsCode = '  alert(1);' . PHP_EOL;

        $asset  = $this->_factory->create('test', $jsCode, [], ['type' => Asset::TYPE_JSX_CODE]);
        $result = $asset->load();

        isClass('JBZoo\Assets\Asset\JsxCode', $asset);
        isSame(Asset::TYPE_JSX_CODE, $result[0]);
        isSame('alert(1);', $result[1]);
    }
}
