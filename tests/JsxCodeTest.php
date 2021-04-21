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

declare(strict_types=1);

namespace JBZoo\PHPUnit;

use JBZoo\Assets\Asset\AbstractAsset;

/**
 * Class AssetJsxCodeTest
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class JsxCodeTest extends PHPUnitAssets
{
    public function testCreateAssetJsCode()
    {
        $jsCode = '  alert(1);' . PHP_EOL;

        $asset = $this->factory->create('test', $jsCode, [], ['type' => AbstractAsset::TYPE_JSX_CODE]);
        $result = $asset->load();

        isClass('JBZoo\Assets\Asset\JsxCode', $asset);
        isSame(AbstractAsset::TYPE_JSX_CODE, $result[0]);
        isSame('alert(1);', $result[1]);
    }
}
