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

use JBZoo\Assets\Factory;

/**
 * Class FactoryTest
 *
 * @package JBZoo\PHPUnit
 */
class FactoryTest extends PHPUnit
{

    /**
     * @var \JBZoo\Assets\Factory
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
        $this->factory = new Factory(__DIR__);
    }

    /**
     * @return void
     */
    public function testDefaultParams()
    {
        $params = $this->factory->params();

        isClass('JBZoo\Data\Data', $params);
        isFalse($params->get('debug'));
        isFalse($params->get('combine'));
    }

    /**
     * @return void
     */
    public function testReloadParams()
    {
        $factory = new Factory(__DIR__, [
            'debug'   => true,
            'combine' => true,
        ]);

        $params = $factory->params();
        isTrue($params->get('debug'));
        isTrue($params->get('combine'));
    }

    /**
     * @return void
     */
    public function testCreateSimpleAsset()
    {
        $asset = $this->factory->create('test', 'path/to/my-file.css');
        isClass('JBZoo\Assets\FileAsset', $asset);
        isSame('test', $asset->getName());
        isSame('path/to/my-file.css', $asset->getSource());
        isSame([], $asset->getDependencies());
        isSame(['type' => 'file'], $asset->getOptions());
    }

    /**
     * @return void
     */
    public function testCreateByDependenciesIsString()
    {
        $asset = $this->factory->create('test', '\path\to/my-file.css', 'uikit');
        isClass('JBZoo\Assets\FileAsset', $asset);
        isSame('test', $asset->getName());
        isSame('\path\to/my-file.css', $asset->getSource());
        isSame(['uikit'], $asset->getDependencies());
        isSame(['type' => 'file'], $asset->getOptions());
    }

    /**
     * @return void
     */
    public function testCreateNotCurrentTypeName()
    {
        $asset = $this->factory->create('test', '\path\to/my-file.css', 'uikit', ['type' => 'FilE']);
        isClass('JBZoo\Assets\FileAsset', $asset);
    }

    /**
     * @return void
     */
    public function testCreateByDependenciesIsArray()
    {
        $asset = $this->factory->create('test', '\path\to/my-file.css', ['uikit', 'jquery-ui']);
        isClass('JBZoo\Assets\FileAsset', $asset);
        isSame('test', $asset->getName());
        isSame('\path\to/my-file.css', $asset->getSource());
        isSame(['uikit', 'jquery-ui'], $asset->getDependencies());
        isSame(['type' => 'file'], $asset->getOptions());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testCreateInvalidArgumentByOptionsString()
    {
        $this->factory->create('test', '\path\to/my-file.css', ['uikit', 'jquery-ui'], 'no-exits');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testCreateInvalidArgumentByOptionsArray()
    {
        $this->factory->create('test', '\path\to/my-file.css', ['uikit', 'jquery-ui'], ['type' => 'no-exits']);
    }

    /**
     * @return void
     */
    public function testGetRoot()
    {
        isSame(__DIR__, $this->factory->getRoot());
    }

    /**
     * @return void
     */
    public function testRegisterCustomTypeByString()
    {
        $this->factory->register('String', 'Custom\Assets\StringAsset');
        $asset = $this->factory->create('custom_name', 'my/custom/path.js', null, 'STRING');
        isClass('Custom\Assets\StringAsset', $asset);
        isSame('custom_name', $asset->getName());
        isSame([], $asset->getDependencies());
        isSame('my/custom/path.js', $asset->getSource());
        isSame(['type' => 'string'], $asset->getOptions());
    }

    /**
     * @return void
     */
    public function testRegisterCustomTypeByArray()
    {
        $this->factory->register('String', 'Custom\Assets\StringAsset');
        $asset = $this->factory->create('custom_name', 'my/custom/path.js', null, ['type' => 'string']);
        isClass('Custom\Assets\StringAsset', $asset);
        isSame('custom_name', $asset->getName());
        isSame([], $asset->getDependencies());
        isSame('my/custom/path.js', $asset->getSource());
        isSame(['type' => 'string'], $asset->getOptions());
    }
}
