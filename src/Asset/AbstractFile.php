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

declare(strict_types=1);

namespace JBZoo\Assets\Asset;

use JBZoo\Assets\Exception;
use JBZoo\Utils\Url;

/**
 * Class AbstractFile
 * @package JBZoo\Assets\Asset
 */
abstract class AbstractFile extends AbstractAsset
{
    public const TYPE = 'abstract';

    /**
     * @inheritDoc
     */
    public function load(): array
    {
        return [static::TYPE, $this->findSource()];
    }

    /**
     * Find source in variants.
     *
     * @return string|null
     * @throws Exception
     * @throws \JBZoo\Path\Exception
     */
    protected function findSource(): ?string
    {
        if (!\is_string($this->source)) {
            throw new Exception('Source must be string type');
        }

        $path = $this->eManager->getPath();

        if ($path->isVirtual($this->source)) {
            $path = $path->get($this->source);

            $isStrictMode = $this->eManager->getParams()->get('strict_mode', false, 'bool');
            if ($isStrictMode && !$path) {
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
