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
 * Class JsCode
 * @package JBZoo\Assets\Asset
 */
class JsCode extends File
{
    protected $_type = Asset::TYPE_JS_CODE;

    /**
     * {@inheritdoc}
     */
    public function load(array $filters = [])
    {
        $source = Str::trim($this->_source, true);

        if (stripos($source, '<script') === 0) {
            if (preg_match('#<script.*?>(.*?)</script>#ius', $source, $matches)) {
                $source = Str::trim($matches[1]);
            }
        }

        return [$this->_type, $source];
    }
}
