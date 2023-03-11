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

final class CssCode extends AbstractAsset
{
    protected string $type = AbstractAsset::TYPE_CSS_CODE;

    public function load(): array
    {
        if (!\is_string($this->source)) {
            throw new Exception('Source must be string type');
        }

        $source = \trim($this->source);

        if ((\stripos($source, '<style') === 0)
            && \preg_match('#<style.*?>(.*?)</style>#ius', $source, $matches) > 0
        ) {
            $source = $matches[1];
        }

        return [$this->type, $source];
    }
}
