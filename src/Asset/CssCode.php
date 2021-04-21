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
 * Class CssCode
 * @package JBZoo\Assets\Asset
 */
final class CssCode extends AbstractAsset
{
    /**
     * @var string
     */
    protected $type = AbstractAsset::TYPE_CSS_CODE;

    /**
     * @inheritDoc
     */
    public function load(): array
    {
        if (!\is_string($this->source)) {
            throw new Exception('Source must be string type');
        }

        $source = \trim($this->source);

        if ((\stripos($source, '<style') === 0) && \preg_match('#<style.*?>(.*?)</style>#ius', $source, $matches)) {
            $source = $matches[1];
        }

        return [$this->type, $source];
    }
}
