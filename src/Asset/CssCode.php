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

use JBZoo\Utils\Str;

/**
 * Class CssCode
 * @package JBZoo\Assets\Asset
 */
class CssCode extends AbstractAsset
{
    /**
     * @var string
     */
    protected $type = AbstractAsset::TYPE_CSS_CODE;

    /**
     * {@inheritdoc}
     */
    public function load(array $filters = [])
    {
        $source = Str::trim($this->source, true);

        if ((stripos($source, '<style') === 0) && preg_match('#<style.*?>(.*?)</style>#ius', $source, $matches)) {
            $source = $matches[1];
        }

        return [$this->type, $source];
    }
}
