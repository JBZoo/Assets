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

namespace JBZoo\Assets\Filter;

use JBZoo\Utils\FS;
use JBZoo\Utils\Str;
use JBZoo\Utils\Slug;
use JBZoo\Assets\FileAsset;

/**
 * Class CompressorAbstract
 *
 * @package JBZoo\Assets\Filter
 */
abstract class CompressorAbstract extends FilterAbstract
{

    /**
     * Code compressing.
     *
     * @param $code
     * @return string
     */
    abstract protected function _compress($code);

    /**
     * Asset file head comment.
     *
     * @return string
     */
    protected function _fileHead()
    {
        $relPath = Slug::filter($this->_asset->getSource(), '_');
        return implode(PHP_EOL, [
            '/* cacheid:' . $this->_getHash() . ' */',
            '/* resource:' . $relPath . ' */',
        ]);
    }

    /**
     * Actual asset hash.
     *
     * @return string
     */
    protected function _getHash()
    {
        /** @var FileAsset $asset */
        $asset  = $this->_asset;
        $params = $asset->getParams()->getArrayCopy();

        $hashed = [
            'params'       => $params,
            'name'         => $asset->getName(),
            'source'       => $asset->getSource(),
            'options'      => $asset->getOptions(),
            'dependencies' => $asset->getDependencies(),
            'file_md5'     => md5_file($asset->getFullPath()),
        ];

        $hashed = serialize($hashed);
        $hash   = md5($hashed);

        return $hash;
    }

    /**
     * Get new file path.
     *
     * @return string
     */
    protected function _getResultFile()
    {
        $params = $this->_asset->getParams();
        $path = FS::clean($params->get('cache_path') . '/' . $this->_newName(), '/');

        return $path;
    }

    /**
     * Check is current cache is expired.
     *
     * @return bool
     */
    protected function _isExpired()
    {
        $newFile    = $this->_getResultFile();
        $actualHash = Str::clean(FS::firstLine($newFile));

        list($expectedHash) = explode(PHP_EOL, $this->_fileHead());

        return $expectedHash !== $actualHash;
    }

    /**
     * New name by hash.
     *
     * @return string
     */
    protected function _newName()
    {
        return $this->_getHash() . '.css';
    }
}
