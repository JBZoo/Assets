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

use JBZoo\Assets\Asset\File;
use JBZoo\Utils\FS;
use JBZoo\Utils\Url;
use JBZoo\Path\Path;
use JBZoo\Assets\Factory;
use JBZoo\Assets\Manager;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ManagerTest
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class ManagerTest_old extends PHPUnit
{
    /**
     * @var Manager
     */
    protected $_manager;

    /**
     * @var Factory
     */
    protected $_factory;

    /**
     * @var string
     */
    protected $_fixtPath;

    /**
     * @var string
     */
    protected $_cachePath;

    /**
     * Setup test data
     */
    public function setUp()
    {
        parent::setUp();

        $_SERVER['HTTP_HOST']   = 'test.dev';
        $_SERVER['REQUEST_URI'] = '/request';

        $this->_fixtPath  = PROJECT_ROOT . '/tests/fixtures';
        $this->_cachePath = PROJECT_ROOT . '/build/cache';

        // cleanup
        $fs = new Filesystem();
        $fs->remove($this->_cachePath);
        $fs->mkdir($this->_cachePath);

        // Prepare lib
        $path = new Path();
        $path->setRoot($this->_fixtPath);

        $this->_factory = new Factory($path, [
            'cache_path' => $this->_cachePath,
            'debug'      => true,
        ]);

        $this->_manager = new Manager($this->_factory);
    }

    public function testRegisterLocalAssets()
    {
        $this->_manager
            ->add('custom', 'assets/css/custom.css')
            ->register('bootstrap', 'assets/css/libs/bootstrap.css')
            ->add('bootstrap');
        $collection = $this->_manager->getCollection();

        /** @var File $asset */
        $asset = $collection->get('bootstrap');

        isSame('bootstrap', $asset->getAlias());
        isSame('css', $asset->getExt());
        isSame(2, $collection->count());
    }

    public function testUnRegisterAssets()
    {
        $this->_manager
            ->add('custom', 'assets/css/custom.css')
            ->register('styles', 'assets/css/styles.css')
            ->register('template', 'assets/css/template.css')
            ->register('bootstrap', 'assets/css/libs/bootstrap.css');
        $collection = $this->_manager->getCollection();

        isSame(4, $collection->count());

        $this->_manager->unregister('styles');

        isSame(3, $collection->count());
        isNull($collection->get('styles'));
    }

    public function testBuildFilesAsset()
    {
        $path = Path::getInstance();
        $path->setRoot($this->_fixtPath);

        $path->add([
            $this->_fixtPath . '/assets_virt',
            $this->_fixtPath . '/assets',
        ], 'assets');

        $this->_manager
            ->register('bootstrap', 'assets:css/libs/bootstrap.css')
            ->add('test_script', 'assets/js/scripts.js', 'jquery')
            ->add('no_exits', 'assets/js/no_exits.js')
            ->register('test_styles', 'assets/css/styles.css')
            ->add('jquery', 'assets/js/jquery.js')
            ->add('jb_demo', 'http://demo.jbzoo.com/assets/js/all.js')
            ->add('custom', 'assets/css/custom.css', ['bootstrap', 'test_styles']);

        $assets = $this->_manager->build();

        isSame(2, count($assets));
        isSame(3, count($assets['js']));
        isSame(3, count($assets['css']));

        isLike('/.*assets\/js\/jquery\.js\?[0-9]/', $assets['js'][0]);
        isLike('/.*assets\/js\/scripts\.js\?[0-9]/', $assets['js'][1]);

        isLike('/.*assets\/css\/libs\/bootstrap\.css\?[0-9]/', $assets['css'][0]);
        isLike('/.*assets\/css\/styles\.css\?[0-9]/', $assets['css'][1]);
        isLike('/.*assets\/css\/custom\.css\?[0-9]/', $assets['css'][2]);
    }

    public function testBuildByLessDependCss()
    {
        $this->_manager
            ->register('styles', 'assets/css/custom.css')
            ->add('custom', 'assets/less/styles.less', 'styles');

        $assets = $this->_manager->build();
        isSame(2, count($assets['css']));

        isLike('/.*assets\/css\/custom\.css\?[0-9]/', $assets['css'][0]);
        isLike('/.*cache\/tests_fixtures_assets_less_styles_less\.css\?[0-9]/', $assets['css'][1]);
    }

    public function testNoFileDuplicate()
    {
        $this->_manager
            ->register('styles', 'assets/css/custom.css')
            ->add('test', 'assets/css/test.css', ['styles', 'custom'])
            ->add('custom', 'assets/less/styles.less', 'styles')
            ->add('duplicate', 'assets/less/styles.less', 'styles')
            ->add('custom');

        $assets = $this->_manager->build();

        isSame(3, count($assets['css']));
        isLike('/.*assets\/css\/custom\.css\?[0-9]/', $assets['css'][0]);
        isLike('/.*cache\/tests_fixtures_assets_less_styles_less\.css\?[0-9]/', $assets['css'][1]);
        isLike('/.*assets\/css\/test\.css\?[0-9]/', $assets['css'][2]);
    }

    public function testAllegedSequenceAssets()
    {
        $this->_manager
            ->add('styles', 'assets/css/styles.css')
            ->register('bootstrap', 'assets/css/libs/bootstrap.css')
            ->add('uikit', 'assets/css/libs/uikit.css', 'bootstrap')
            ->add('test', 'assets/css/test.css', ['styles', 'custom'])
            ->add('custom', 'assets/css/custom.css', ['bootstrap']);

        $assets = $this->_manager->build();

        isSame(5, count($assets['css']));
        isLike('/.*assets\/css\/styles\.css\?[0-9]/', $assets['css'][0]);
        isLike('/.*assets\/css\/libs\/bootstrap\.css\?[0-9]/', $assets['css'][1]);
        isLike('/.*assets\/css\/libs\/uikit\.css\?[0-9]/', $assets['css'][2]);
        isLike('/.*assets\/css\/custom\.css\?[0-9]/', $assets['css'][3]);
        isLike('/.*assets\/css\/test\.css\?[0-9]/', $assets['css'][4]);
    }

    /**
     * @expectedException \JBZoo\Assets\Exception
     */
    public function testNotAllowedAssetExtension()
    {
        $this->_manager
            ->add('styles', 'assets/css/styles.php')
            ->build();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCircularAssetDependency()
    {
        $this->_manager
            ->add('styles', 'assets/css/styles.css', 'test')
            ->register('bootstrap', 'assets/css/libs/bootstrap.css')
            ->add('uikit', 'assets/css/libs/uikit.css', 'bootstrap')
            ->add('test', 'assets/css/test.css', ['styles', 'custom'])
            ->add('custom', 'assets/css/custom.css', ['bootstrap'])
            ->build();
    }

    public function testLoadByHttpsProtocol()
    {
        $_SERVER['HTTPS']             = 'on';
        $_SERVER['SERVER_PORT']       = 443;
        $_SERVER['X-FORWARDED-PROTO'] = 'https';

        isTrue(Url::isHttps());

        $this->_manager
            ->add('demo', 'http://demo.jbzoo.com/assets/js/all.js', 'jquery')
            ->add('yandex', '//yastatic.net/es5-shims/0.0.2/es5-shims.min.js')
            ->add('jquery', 'assets/js/jquery.js');

        $assets = $this->_manager->build();

        isSame(3, count($assets['js']));
        isLike('/.*assets\/js\/jquery\.js\?[0-9]/', $assets['js'][0]);
        isSame('http://demo.jbzoo.com/assets/js/all.js', $assets['js'][1]);
        isSame('//yastatic.net/es5-shims/0.0.2/es5-shims.min.js', $assets['js'][2]);
    }

    public function testBuildWithCssFilterCompress()
    {
        return;
        skip();
        $factory = new Factory($this->_fixtPath, [
            'cache_path' => $this->_cachePath,
            'debug'      => true,
            'minify_css' => true,
        ]);

        $manager = new Manager($factory);

        $manager
            ->add('custom', 'assets/css/custom.css')
            ->add('styles', 'assets/css/styles.css', 'custom');

        $result = $manager->build(['CssCompressor']);

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //  Check file 1.
        $path1 = FS::clean(str_replace(Url::root(), $this->_fixtPath, $result['css'][0]));
        dump($result['css'][0]);
        list($path1) = explode('?', $path1);
        $strCount = explode(PHP_EOL, file_get_contents($path1));

        isSame(3, count($strCount));
        isTrue(file_exists($path1));
        isSame(2, count($result['css']));
        isSame($strCount[2], '.container{padding-top:20px;padding-bottom:20px}.t3-sl-2{padding:0 15px}');

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //  Check file 2.
        $path1 = FS::clean(str_replace(Url::root(), $this->_fixtPath, $result['css'][1]));
        list($path1) = explode('?', $path1);
        $strCount = explode(PHP_EOL, file_get_contents($path1));

        isSame(3, count($strCount));
        isTrue(file_exists($path1));
        isSame(2, count($result['css']));
        isSame($strCount[2], 'body{background:red}.lang{text-align:right;padding-top:12px}');
    }

    public function testBuildNoCompressCss()
    {
        $this->_manager->add('custom', 'assets/css/custom.css');
        $result = $this->_manager->build(['CssCompressor']);

        isSame(1, count($result['css']));
        isTrue(strpos($result['css'][0], 'custom.css'));
    }

    /**
     * @expectedException \JBZoo\Assets\Exception
     */
    public function testBuildNoCompressAndNoExistsCss()
    {
        $this->_manager->add('custom', 'assets/css/no-exists.css');
        $this->_manager->build(['CssCompressor']);
    }

    public function testBuildNoCompressAndNotAssertFile()
    {
        $factory = new Factory(__DIR__, [
            'cache_path' => $this->_cachePath,
            'debug'      => true,
            'minify_css' => true,
        ]);

        $factory->register('Custom', 'Custom\Assets\CustomAsset');

        $manager = new Manager($factory);
        $manager->add('jquery', 'assets/js/jquery.js', null, 'custom');

        $build = $manager->build(['CssCompressor']);

        //  Empty because CustomAsset is not File. See JBZoo\Assets\Filter\CssCompressor
        isSame([], $build['js']);
    }
}
