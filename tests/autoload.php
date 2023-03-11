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

use function JBZoo\PHPUnit\isSame;

if (!\defined('ROOT_PATH')) { // for PHPUnit process isolation
    \define('ROOT_PATH', \realpath('.'));
}

// main autoload
if ($autoload = \realpath('./vendor/autoload.php')) {
    require_once $autoload;
} else {
    echo 'Please execute "composer update" !' . \PHP_EOL;
    exit(1);
}

/**
 * @param null|string $message
 */
function isSamePath($excpected, $actual, string $message = ''): void
{
    isSame($excpected, $actual, $message);
}
