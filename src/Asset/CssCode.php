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

use JBZoo\Utils\Str;

/**
 * Class CssCode
 * @package JBZoo\Assets\Asset
 */
class CssCode extends Asset
{
    protected $_type = Asset::TYPE_CSS_CODE;

    /**
     * {@inheritdoc}
     */
    public function load(array $filters = [])
    {
        $source = Str::trim($this->_source, true);

        if (stripos($source, '<style') === 0) {
            if (preg_match('#<style.*?>(.*?)</style>#ius', $source, $matches)) {
                $source = Str::trim($matches[1]);
            }
        }

        return [$this->_type, $source];
    }
}
