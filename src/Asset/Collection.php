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

/**
 * Class Collection
 * @package JBZoo\Assets\Asset
 */
class Collection extends Asset
{
    /**
     * {@inheritdoc}
     */
    public function load(array $filters = [])
    {
        $factory = $this->_manager->getFactory();

        $result = [];
        foreach ($this->_source as $key => $source) {
            $subAlias = $this->_alias . '-' . $key;
            $asset    = $factory->create($subAlias, $source, $this->_dependencies, $this->_options);
            $result[] = $asset->load($filters);
        }

        return [Asset::TYPE_COLLECTION, $result];
    }
}
