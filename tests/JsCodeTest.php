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
class JsCodeTest extends PHPUnitAssets
{
    public function testCreateAssetJsCode(): void
    {
        $jsCode = '  alert(1);' . \PHP_EOL;

        $asset  = $this->factory->create('test', $jsCode, [], ['type' => AbstractAsset::TYPE_JS_CODE]);
        $result = $asset->load();

        isClass('JBZoo\Assets\Asset\JsCode', $asset);
        isSame(AbstractAsset::TYPE_JS_CODE, $result[0]);
        isSame('alert(1);', $result[1]);
    }

    public function testCreateAssetJsCodeWithTags(): void
    {
        $jsCode       = 'alert(1);';
        $draftJsCode  = \PHP_EOL . $jsCode . \PHP_EOL;
        $jsCodeTagged = ' <script>' . $draftJsCode . '</script> ';

        $asset  = $this->factory->create('test', $jsCodeTagged, [], ['type' => AbstractAsset::TYPE_JS_CODE]);
        $result = $asset->load();

        isSame(AbstractAsset::TYPE_JS_CODE, $result[0]);
        isSame($draftJsCode, $result[1]);
    }

    public function testCreateAssetJsCodeWithTagsAndAttrs(): void
    {
        $jsCode       = 'alert(1);';
        $draftJsCode  = ' ' . \PHP_EOL . $jsCode . \PHP_EOL . ' ';
        $jsCodeTagged = ' <Script type="text/javascript">' . $draftJsCode . '</ScripT> ';

        $asset  = $this->factory->create('test', $jsCodeTagged, [], ['type' => AbstractAsset::TYPE_JS_CODE]);
        $result = $asset->load();

        isSame(AbstractAsset::TYPE_JS_CODE, $result[0]);
        isSame($draftJsCode, $result[1]);
    }
}
