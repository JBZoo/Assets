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

/**
 * Class JsxCode
 * @package JBZoo\Assets\Asset
 */
final class JsxCode extends JsCode
{
    /**
     * @var string
     */
    protected string $type = AbstractAsset::TYPE_JSX_CODE;
}
