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

use JBZoo\Path\Path;
use JBZoo\Assets\Collection;
use JBZoo\Assets\Factory;
use JBZoo\Assets\Manager;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class PHPUnitAssets
 * @package JBZoo\PHPUnit
 * @SuppressWarnings(PHPMD.Superglobals)
 */
abstract class PHPUnitAssets extends PHPUnit
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
     * @var Collection
     */
    protected $_collection;

    /**
     * @var Path
     */
    protected $_path;

    /**
     * @var Filesystem
     */
    protected $_fs;

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
        $_SERVER['HTTP_HOST']   = 'test.dev';
        $_SERVER['REQUEST_URI'] = '/request';

        $this->_fixtPath  = PROJECT_ROOT . '/tests/fixtures';
        $this->_cachePath = PROJECT_ROOT . '/build/cache';

        // cleanup
        $this->_fs = new Filesystem();
        $this->_fs->remove($this->_cachePath);
        $this->_fs->mkdir($this->_cachePath);

        // Prepare lib
        $this->_path = new Path();
        $this->_path->setRoot($this->_fixtPath);
        $this->_path->set('assets', 'root:assets');

        $this->_manager = new Manager($this->_path);

        $this->_factory    = $this->_manager->getFactory();
        $this->_collection = $this->_manager->getCollection();
    }
}
