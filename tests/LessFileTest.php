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
 * Class AssetLessFileTest
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class LessFileTest extends PHPUnitAssets
{
    public function testLessCompiler()
    {
        $this->manager->setParam('less', [
            'cache_path' => $this->cachePath,
        ]);

        $asset = $this->factory->create('test', 'assets:less/styles.less');

        $result = $asset->load();
        isSame(AbstractAsset::TYPE_CSS_FILE, $result[0]); // Less => CSS
        isSamePath(PROJECT_ROOT . '/build/cache/tests_fixtures_assets_less_styles_less.css', $result[1]);
        isContain('.myClass-block', file_get_contents($result[1]));
    }

    public function testLessTryToFindUndefinedFile()
    {
        $this->manager->setParam('less', [
            'cache_path' => $this->cachePath,
        ]);

        $asset = $this->factory->create('test', 'assets:less/undefined.less');

        $result = $asset->load();
        isSame(AbstractAsset::TYPE_CSS_FILE, $result[0]);
        isFalse($result[1]);
    }
}
