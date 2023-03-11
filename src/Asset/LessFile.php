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

use JBZoo\Less\Less;

/**
 * Class LessFile
 * @package JBZoo\Assets\Asset
 */
final class LessFile extends AbstractFile
{
    public const TYPE = AbstractAsset::TYPE_LESS_FILE;

    /**
     * @inheritDoc
     */
    public function load(): array
    {
        $result = parent::load();
        $compiled = null;

        if ($result[1]) {
            $options = $this->eManager->getParams();
            $root = $this->eManager->getPath()->getRoot();
            $less = new Less($options->get('less'));
            $compiled = $less->compile($result[1], $root);
        }

        return [AbstractAsset::TYPE_CSS_FILE, $compiled];
    }
}
