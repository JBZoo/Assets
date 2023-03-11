<?php

/**
 * JBZoo Toolbox - Assets.
 *
 * This file is part of the JBZoo Toolbox project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @see        https://github.com/JBZoo/Assets
 */

declare(strict_types=1);

namespace JBZoo\Assets\Asset;

use JBZoo\Assets\Exception;

final class Collection extends AbstractAsset
{
    public function load(): array
    {
        $factory = $this->eManager->getFactory();

        $result = [];
        if (\is_array($this->source)) {
            foreach ($this->source as $key => $source) {
                $subAlias = $this->alias . '-' . $key;
                $asset    = $factory->create($subAlias, $source, $this->dependencies, $this->options);
                $result[] = $asset->load();
            }
        } else {
            throw new Exception("Source must be array. Current value: {$this->source}");
        }

        return [AbstractAsset::TYPE_COLLECTION, $result];
    }
}
