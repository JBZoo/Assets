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
 * Class AssetCollectionTest
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class AssetCollectionTest extends PHPUnitAssets
{
    public function testCreateAssetCollection()
    {
        $variable = 0;

        $this->_manager->setParam('less', [
            'cache_path' => $this->_cachePath,
        ]);

        $this->_manager->register(
            'pack',
            [
                'assets:js/jquery.js',
                'assets/js/test.js',
                'assets/less/styles.less',
                function () use (&$variable) {
                    $variable++;
                },
            ]
        );

        $this->_manager->register('test-style', 'assets\css\styles.css', 'pack');
        $this->_manager->add('external', '//yandes.st/script.js?v=42', 'test-style');

        $result = $this->_manager->build();
        $css    = $result[Asset::TYPE_CSS_FILE];
        $js     = $result[Asset::TYPE_JS_FILE];

        // Check CSS
        isSamePath(PROJECT_ROOT . '/build/cache/tests_fixtures_assets_less_styles_less.css', $css[0]);
        isContain('.myClass-block', file_get_contents($css[0]));
        isSamePath(PROJECT_ROOT . '/tests/fixtures/assets/css/styles.css', $css[1]);

        // Check JS
        isSamePath(PROJECT_ROOT . '/tests/fixtures/assets/js/jquery.js', $js[0]);
        isSamePath(PROJECT_ROOT . '/tests/fixtures/assets/js/test.js', $js[1]);
        isSamePath('//yandes.st/script.js?v=42', $js[2]);

        // Check Callback
        isSame(1, $variable);
    }
}