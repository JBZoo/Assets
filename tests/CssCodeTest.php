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
 * Class AssetCssCodeTest
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class CssCodeTest extends PHPUnitAssets
{
    public function testCreateAssetCssCode()
    {
        $cssCode = '  div{color:red;}' . PHP_EOL;

        $asset = $this->factory->create('test', $cssCode, [], ['type' => AbstractAsset::TYPE_CSS_CODE]);
        $result = $asset->load();

        isClass('JBZoo\Assets\Asset\CssCode', $asset);
        isSame(AbstractAsset::TYPE_CSS_CODE, $result[0]);
        isSame('div{color:red;}', $result[1]);
    }

    public function testCreateAssetCssCodeWithTags()
    {
        $cssCode = 'div{color:red;}';
        $draftCssCode = PHP_EOL . $cssCode . ' ' . PHP_EOL;
        $cssCodeTagged = ' <Style>' . $draftCssCode . '</StylE> ';

        $asset = $this->factory->create('test', $cssCodeTagged, [], ['type' => AbstractAsset::TYPE_CSS_CODE]);
        $result = $asset->load();

        isSame(AbstractAsset::TYPE_CSS_CODE, $result[0]);
        isSame($draftCssCode, $result[1]);
    }

    public function testCreateAssetCssCodeWithTagsAndAttrs()
    {
        $cssCode = 'div{color:red;}';
        $draftCssCode = '   ' . PHP_EOL . $cssCode . ' ' . PHP_EOL . ' ';
        $cssCodeTagged = ' <Style someattr="123">' . $draftCssCode . '</StylE> ';

        $asset = $this->factory->create('test', $cssCodeTagged, [], ['type' => AbstractAsset::TYPE_CSS_CODE]);
        $result = $asset->load();

        isSame(AbstractAsset::TYPE_CSS_CODE, $result[0]);
        isSame($draftCssCode, $result[1]);
    }
}
