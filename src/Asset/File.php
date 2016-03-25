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

namespace JBZoo\Assets\Asset;

use JBZoo\Utils\Url;

/**
 * Class File
 * @package JBZoo\Assets\Asset
 */
abstract class File extends Asset
{
    /**
     * @var string
     */
    protected $_type = null;

    /**
     * {@inheritdoc}
     */
    public function load(array $filters = [])
    {
        return [$this->_type, $this->_findSource()];
    }

    /**
     * Find source in variants.
     *
     * @return array
     */
    protected function _findSource()
    {
        $path = $this->_manager->getPath();
        if ($path->isVirtual($this->_source)) {
            return $path->get($this->_source);
        }

        if (Url::isAbsolute($this->_source)) {
            return $this->_source;
        }

        $fullPath = $path->get('root:' . $this->_source);

        return $fullPath;
    }
}
