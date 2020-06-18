<?php

/**
 * JBZoo Toolbox - Assets
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Assets
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Assets
 */

namespace JBZoo\Assets\Asset;

use JBZoo\Assets\Exception;

/**
 * Class Collection
 * @package JBZoo\Assets\Asset
 */
class Collection extends AbstractAsset
{
    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function load(array $filters = [])
    {
        $factory = $this->eManager->getFactory();

        $result = [];
        foreach ($this->source as $key => $source) {
            $subAlias = $this->alias . '-' . $key;
            $asset = $factory->create($subAlias, $source, $this->dependencies, $this->options);
            $result[] = $asset->load($filters);
        }

        return [AbstractAsset::TYPE_COLLECTION, $result];
    }
}
