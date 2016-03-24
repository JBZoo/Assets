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
 * @package JBZoo\PHPUnit
 */
class FactoryTest extends PHPUnit
{
    /**
     * @var Factory
     */
    protected $_factory;

    /**
     * Setup test data
     */
    public function setUp()
    {
        parent::setUp();
        $this->_factory = new Factory(__DIR__);
    }

    public function testDefaultParams()
    {
        $params = $this->_factory->params();

        isClass('JBZoo\Data\Data', $params);
        isFalse($params->get('debug'));
        isFalse($params->get('combine'));
    }

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

    public function testCreateSimpleAsset()
    {
        $asset = $this->_factory->create('test', 'path/to/my-file.css');

        isClass('JBZoo\Assets\FileAsset', $asset);
        isSame('test', $asset->getName());
        isSame('path/to/my-file.css', $asset->getSource());
        isSame([], $asset->getDependencies());
        isSame(['type' => 'file'], $asset->getOptions());
    }

    public function testCreateByDependenciesIsString()
    {
        $asset = $this->_factory->create('test', '\path\to/my-file.css', 'uikit');

        isClass('JBZoo\Assets\FileAsset', $asset);
        isSame('test', $asset->getName());
        isSame('\path\to/my-file.css', $asset->getSource());
        isSame(['uikit'], $asset->getDependencies());
        isSame(['type' => 'file'], $asset->getOptions());
    }

    public function testCreateNotCurrentTypeName()
    {
        $asset = $this->_factory->create('test', '\path\to/my-file.css', 'uikit', ['type' => 'FilE']);

        isClass('JBZoo\Assets\FileAsset', $asset);
    }

    public function testCreateByDependenciesIsArray()
    {
        $asset = $this->_factory->create('test', '\path\to/my-file.css', ['uikit', 'jquery-ui']);

        isClass('JBZoo\Assets\FileAsset', $asset);
        isSame('test', $asset->getName());
        isSame('\path\to/my-file.css', $asset->getSource());
        isSame(['uikit', 'jquery-ui'], $asset->getDependencies());
        isSame(['type' => 'file'], $asset->getOptions());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateInvalidArgumentByOptionsString()
    {
        $this->_factory->create('test', '\path\to/my-file.css', ['uikit', 'jquery-ui'], 'no-exits');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateInvalidArgumentByOptionsArray()
    {
        $this->_factory->create('test', '\path\to/my-file.css', ['uikit', 'jquery-ui'], ['type' => 'no-exits']);
    }

    public function testGetRoot()
    {
        isSame(__DIR__, $this->_factory->getRoot());
    }

    public function testRegisterCustomTypeByString()
    {
        $this->_factory->register('Custom', 'Custom\Assets\CustomAsset');
        $asset = $this->_factory->create('custom_name', 'my/custom/path.js', null, 'CUSTOM');

        isClass('Custom\Assets\CustomAsset', $asset);
        isSame('custom_name', $asset->getName());
        isSame([], $asset->getDependencies());
        isSame('my/custom/path.js', $asset->getSource());
        isSame(['type' => 'custom'], $asset->getOptions());
    }

    public function testRegisterCustomTypeByArray()
    {
        $this->_factory->register('Custom', 'Custom\Assets\CustomAsset');
        $asset = $this->_factory->create('custom_name', 'my/custom/path.js', null, ['type' => 'custom']);

        isClass('Custom\Assets\CustomAsset', $asset);
        isSame('custom_name', $asset->getName());
        isSame([], $asset->getDependencies());
        isSame('my/custom/path.js', $asset->getSource());
        isSame(['type' => 'custom'], $asset->getOptions());
    }
}
