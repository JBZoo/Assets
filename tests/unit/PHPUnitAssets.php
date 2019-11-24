<?php
/**
 * JBZoo Assets
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Assets
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Assets
 */

namespace JBZoo\PHPUnit;

use JBZoo\Assets\Collection;
use JBZoo\Assets\Factory;
use JBZoo\Assets\Manager;
use JBZoo\Path\Path;
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
    protected $manager;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var Path
     */
    protected $path;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var string
     */
    protected $fixtPath;

    /**
     * @var string
     */
    protected $cachePath;

    /**
     * Setup test data
     */
    protected function setUp(): void
    {
        $_SERVER['HTTP_HOST'] = 'test.dev';
        $_SERVER['REQUEST_URI'] = '/request';

        $this->fixtPath = PROJECT_ROOT . '/tests/fixtures';
        $this->cachePath = PROJECT_ROOT . '/build/cache';

        // cleanup
        $this->fs = new Filesystem();
        $this->fs->remove($this->cachePath);
        $this->fs->mkdir($this->cachePath);

        // Prepare lib
        $this->path = new Path();
        $this->path->setRoot($this->fixtPath);
        $this->path->set('assets', 'root:assets');

        $this->manager = new Manager($this->path);

        $this->factory = $this->manager->getFactory();
        $this->collection = $this->manager->getCollection();
    }
}
