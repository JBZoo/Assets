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
 * Class AssetCssCodeTest
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class AssetCssCodeTest extends PHPUnitAssets
{
    public function testCreateAssetCssCode()
    {
        $cssCode = '  div{color:red;}' . PHP_EOL;

        $asset  = $this->_factory->create('test', $cssCode, [], ['type' => Asset::TYPE_CSS_CODE]);
        $result = $asset->load();

        isClass('JBZoo\Assets\Asset\CssCode', $asset);
        isSame(Asset::TYPE_CSS_CODE, $result[0]);
        isSame('div{color:red;}', $result[1]);
    }

    public function testCreateAssetCssCodeWithTags()
    {
        $cssCode       = 'div{color:red;}';
        $draftCssCode  = PHP_EOL . $cssCode . ' ' . PHP_EOL;
        $cssCodeTagged = ' <Style>' . $draftCssCode . '</StylE> ';

        $asset  = $this->_factory->create('test', $cssCodeTagged, [], ['type' => Asset::TYPE_CSS_CODE]);
        $result = $asset->load();

        isSame(Asset::TYPE_CSS_CODE, $result[0]);
        isSame($draftCssCode, $result[1]);
    }

    public function testCreateAssetCssCodeWithTagsAndAttrs()
    {
        $cssCode       = 'div{color:red;}';
        $draftCssCode  = '   ' . PHP_EOL . $cssCode . ' ' . PHP_EOL . ' ';
        $cssCodeTagged = ' <Style someattr="123">' . $draftCssCode . '</StylE> ';

        $asset  = $this->_factory->create('test', $cssCodeTagged, [], ['type' => Asset::TYPE_CSS_CODE]);
        $result = $asset->load();

        isSame(Asset::TYPE_CSS_CODE, $result[0]);
        isSame($draftCssCode, $result[1]);
    }
}
