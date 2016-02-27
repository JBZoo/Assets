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

namespace JBZoo\Assets;

/**
 * Interface AssetInterface
 *
 * @package JBZoo\Assets
 */
interface AssetInterface
{

    /**
     * Gets the name.
     *
     * @return string
     */
    public function getName();

    /**
     * Gets the source.
     *
     * @return string
     */
    public function getSource();

    /**
     * Gets the dependencies.
     *
     * @return array
     */
    public function getDependencies();

    /**
     * Gets the type.
     *
     * @return array
     */
    public function getOptions();
}
