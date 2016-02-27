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

use JBZoo\Utils\Url;
use JBZoo\Path\Path;
use JBZoo\Assets\Factory;
use JBZoo\Assets\Manager;
use JBZoo\Assets\FileAsset;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ManagerTest
 *
 * @package JBZoo\PHPUnit
 */
class ManagerTest extends PHPUnit
{

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * Setup test data.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $_SERVER['HTTP_HOST']   = 'test.dev';
        $_SERVER['SERVER_PORT'] = 80;
        $_SERVER['REQUEST_URI'] = '/request';

        $cachePath = __DIR__ . '/cache';

        $this->factory = new Factory(__DIR__, [
            'cache_path' => $cachePath,
            'debug' => true,
        ]);

        $this->manager = new Manager($this->factory);
    }

    /**
     * @return void
     */
    public function testRegisterLocalAssets()
    {
        $this->manager
            ->add('custom', 'assets/css/custom.css')
            ->register('bootstrap', 'assets/css/libs/bootstrap.css')
            ->add('bootstrap');
        $collection = $this->manager->collection();

        /** @var FileAsset $asset */
        $asset = $collection->get('bootstrap');

        isSame('bootstrap', $asset->getName());
        isSame('css', $asset->getExt());
        isSame(2, $collection->count());
    }

    /**
     * @return void
     */
    public function testUnRegisterAssets()
    {
        $this->manager
            ->add('custom', 'assets/css/custom.css')
            ->register('styles', 'assets/css/styles.css')
            ->register('template', 'assets/css/template.css')
            ->register('bootstrap', 'assets/css/libs/bootstrap.css');
        $collection = $this->manager->collection();

        isSame(4, $collection->count());

        $this->manager->unRegister('styles');

        isSame(3, $collection->count());
        isNull($collection->get('styles'));
    }

    /**
     * @return void
     */
    public function testBuildFilesAsset()
    {
        $path = Path::getInstance();
        $path->setRoot(__DIR__);

        $path->add([
            __DIR__ . '/assets_virt',
            __DIR__ . '/assets',
        ], 'assets');

        $this->manager
            ->register('bootstrap', 'assets:css/libs/bootstrap.css')
            ->add('test_script', 'assets/js/scripts.js', 'jquery')
            ->add('no_exits', 'assets/js/no_exits.js')
            ->register('test_styles', 'assets/css/styles.css')
            ->add('jquery', 'assets/js/jquery.js')
            ->add('jb_demo', 'http://demo.jbzoo.com/assets/js/all.js')
            ->add('custom', 'assets/css/custom.css', ['bootstrap', 'test_styles']);

        $assets = $this->manager->build();

        isSame(2, count($assets));
        isSame(3, count($assets['js']));
        isSame(3, count($assets['css']));

        $this->assertRegExp('/.*assets\/js\/jquery\.js\?[0-9]/', $assets['js'][0]);
        $this->assertRegExp('/.*assets\/js\/scripts\.js\?[0-9]/', $assets['js'][1]);

        $this->assertRegExp('/.*assets\/css\/libs\/bootstrap\.css\?[0-9]/', $assets['css'][0]);
        $this->assertRegExp('/.*assets\/css\/styles\.css\?[0-9]/', $assets['css'][1]);
        $this->assertRegExp('/.*assets\/css\/custom\.css\?[0-9]/', $assets['css'][2]);
    }

    /**
     * @return void
     */
    public function testBuildByLessDependCss()
    {
        $this->manager
            ->register('styles', 'assets/css/custom.css')
            ->add('custom', 'assets/less/styles.less', 'styles');

        $assets = $this->manager->build();
        isSame(2, count($assets['css']));

        $this->assertRegExp('/.*assets\/css\/custom\.css\?[0-9]/', $assets['css'][0]);
        $this->assertRegExp('/.*cache\/tests_assets_less_styles_less\.css\?[0-9]/', $assets['css'][1]);
        $this->_removeCache();
    }

    /**
     * @return void
     */
    public function testNoFileDuplicate()
    {
        $this->manager
            ->register('styles', 'assets/css/custom.css')
            ->add('test', 'assets/css/test.css', ['styles', 'custom'])
            ->add('custom', 'assets/less/styles.less', 'styles')
            ->add('duplicate', 'assets/less/styles.less', 'styles')
            ->add('custom');

        $assets = $this->manager->build();

        isSame(3, count($assets['css']));
        $this->assertRegExp('/.*assets\/css\/custom\.css\?[0-9]/', $assets['css'][0]);
        $this->assertRegExp('/.*cache\/tests_assets_less_styles_less\.css\?[0-9]/', $assets['css'][1]);
        $this->assertRegExp('/.*assets\/css\/test\.css\?[0-9]/', $assets['css'][2]);
        $this->_removeCache();
    }

    /**
     * @return void
     */
    public function testAllegedSequenceAssets()
    {
        $this->manager
            ->add('styles', 'assets/css/styles.css')
            ->register('bootstrap', 'assets/css/libs/bootstrap.css')
            ->add('uikit', 'assets/css/libs/uikit.css', 'bootstrap')
            ->add('test', 'assets/css/test.css', ['styles', 'custom'])
            ->add('custom', 'assets/css/custom.css', ['bootstrap']);

        $assets = $this->manager->build();

        isSame(5, count($assets['css']));
        $this->assertRegExp('/.*assets\/css\/styles\.css\?[0-9]/', $assets['css'][0]);
        $this->assertRegExp('/.*assets\/css\/libs\/bootstrap\.css\?[0-9]/', $assets['css'][1]);
        $this->assertRegExp('/.*assets\/css\/libs\/uikit\.css\?[0-9]/', $assets['css'][2]);
        $this->assertRegExp('/.*assets\/css\/custom\.css\?[0-9]/', $assets['css'][3]);
        $this->assertRegExp('/.*assets\/css\/test\.css\?[0-9]/', $assets['css'][4]);
    }

    /**
     * @return void
     * @expectedException \JBZoo\Assets\Exception
     */
    public function testNotAllowedAssetExtension()
    {
        $this->manager
            ->add('styles', 'assets/css/styles.php')
            ->build();
    }

    /**
     * @return void
     * @expectedException \RuntimeException
     */
    public function testCircularAssetDependency()
    {
        $this->manager
            ->add('styles', 'assets/css/styles.css', 'test')
            ->register('bootstrap', 'assets/css/libs/bootstrap.css')
            ->add('uikit', 'assets/css/libs/uikit.css', 'bootstrap')
            ->add('test', 'assets/css/test.css', ['styles', 'custom'])
            ->add('custom', 'assets/css/custom.css', ['bootstrap'])
            ->build();
    }

    /**
     * @return void
     */
    public function testLoadByHttpsProtocol()
    {
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_PORT'] = 443;
        $_SERVER['X-FORWARDED-PROTO'] = 'https';

        isTrue(Url::isHttps());

        $this->manager
            ->add('demo', 'http://demo.jbzoo.com/assets/js/all.js', 'jquery')
            ->add('yandex', '//yastatic.net/es5-shims/0.0.2/es5-shims.min.js')
            ->add('jquery', 'assets/js/jquery.js');

        $assets = $this->manager->build();

        isSame(3, count($assets['js']));
        $this->assertRegExp('/.*assets\/js\/jquery\.js\?[0-9]/', $assets['js'][0]);
        isSame('http://demo.jbzoo.com/assets/js/all.js', $assets['js'][1]);
        isSame('//yastatic.net/es5-shims/0.0.2/es5-shims.min.js', $assets['js'][2]);
    }

    /**
     * @return void
     */
    protected function _removeCache()
    {
        $fs = new Filesystem();
        $cachePath = __DIR__ . '/cache';
        $fs->remove($cachePath);
    }
}
