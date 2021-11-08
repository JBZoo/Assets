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

namespace JBZoo\PHPUnit;

/**
 * Class CodestyleReadmeTest
 *
 * @package JBZoo\PHPUnit
 */
class AssetsReadmeTest extends AbstractReadmeTest
{
    protected $packageName = 'Assets';

    protected function setUp(): void
    {
        parent::setUp();
        $this->params['strict_types'] = true;
        $this->params['travis'] = false;
    }
}
