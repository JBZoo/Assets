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
use JBZoo\Utils\Url;

abstract class AbstractFile extends AbstractAsset
{
    public const TYPE = 'abstract';

    public function load(): array
    {
        return [static::TYPE, $this->findSource()];
    }

    /**
     * Find source in variants.
     */
    protected function findSource(): ?string
    {
        if (!\is_string($this->source)) {
            throw new Exception('Source must be string type');
        }

        $path = $this->eManager->getPath();

        if ($path->isVirtual($this->source)) {
            $path = $path->get($this->source);

            $isStrictMode = $this->eManager->getParams()->getBool('strict_mode');
            if ($isStrictMode && ($path === null || $path === '')) {
                throw new Exception("Asset file not found: {$this->source}");
            }

            return $path;
        }

        if (Url::isAbsolute($this->source)) {
            return $this->source;
        }

        return $path->get('root:' . $this->source);
    }
}
