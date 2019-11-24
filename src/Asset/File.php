<?php
/**
 * JBZoo Assets
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Assets
 * @license    MIT
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/Assets
 */

namespace JBZoo\Assets\Asset;

use JBZoo\Assets\Exception;
use JBZoo\Utils\Url;

/**
 * Class File
 * @package JBZoo\Assets\Asset
 */
abstract class File extends Asset
{
    /**
     * @var string
     */
    protected $type;

    /**
     * {@inheritdoc}
     */
    public function load(array $filters = [])
    {
        return [$this->type, $this->findSource()];
    }

    /**
     * Find source in variants.
     *
     * @return string|array
     * @throws Exception
     * @throws \JBZoo\Path\Exception
     */
    protected function findSource()
    {
        $path = $this->eManager->getPath();

        if ($path->isVirtual($this->source)) {
            $path = $path->get($this->source);

            $isStrictMode = $this->eManager->getParams()->get('strict_mode', false, 'bool');
            if ($isStrictMode && !$path) {
                throw new Exception("Asset file not found: {$this->source}");
            }

            return $path;
        }

        if (Url::isAbsolute($this->source)) {
            return $this->source;
        }

        return $path->get('root:' . $this->source);
    }
}
