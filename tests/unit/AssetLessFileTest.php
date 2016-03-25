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
 * Class AssetLessFileTest
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class AssetLessFileTest extends PHPUnitAssets
{
    public function testLessCompiler()
    {
        $this->_manager->setParam('less', [
            'cache_path' => $this->_cachePath,
        ]);

        $asset = $this->_factory->create('test', 'assets:less/styles.less');

        $result = $asset->load();
        isSame(Asset::TYPE_CSS_FILE, $result[0]); // Less => CSS
        isSamePath(PROJECT_ROOT . '/build/cache/tests_fixtures_assets_less_styles_less.css', $result[1]);
        isContain('.myClass-block', file_get_contents($result[1]));
    }

    public function testLessTryToFindUndefinedFile()
    {
        $this->_manager->setParam('less', [
            'cache_path' => $this->_cachePath,
        ]);

        $asset = $this->_factory->create('test', 'assets:less/undefined.less');

        $result = $asset->load();
        isSame(Asset::TYPE_CSS_FILE, $result[0]);
        isFalse($result[1]);
    }
}
