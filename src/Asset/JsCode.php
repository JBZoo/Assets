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

/**
 * Class JsCode
 * @package JBZoo\Assets\Asset
 */
class JsCode extends AbstractFile
{
    /**
     * @var string
     */
    protected string $type = AbstractAsset::TYPE_JS_CODE;

    /**
     * @inheritDoc
     */
    public function load(): array
    {
        if (!\is_string($this->source)) {
            throw new Exception('Source must be string type');
        }

        $source = \trim($this->source);

        if ((\stripos($source, '<script') === 0) && \preg_match('#<script.*?>(.*?)</script>#ius', $source, $matches)) {
            $source = $matches[1];
        }

        return [$this->type, $source];
    }
}
