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

use JBZoo\Less\Less;

/**
 * Class LessFile
 * @package JBZoo\Assets\Asset
 */
class LessFile extends File
{
    protected $_type = Asset::TYPE_LESS_FILE;

    /**
     * {@inheritdoc}
     */
    public function load(array $filters = [])
    {
        $result   = parent::load($filters);
        $compiled = null;

        if ($result[1]) {
            $options  = $this->_manager->getParams();
            $root     = $this->_manager->getPath()->getRoot();
            $less     = new Less($options->get('less'));
            $compiled = $less->compile($result[1], $root);
        }

        return [Asset::TYPE_CSS_FILE, $compiled];
    }
}
