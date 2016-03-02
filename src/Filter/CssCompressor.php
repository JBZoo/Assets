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
use JBZoo\Assets\Exception;
use JBZoo\Assets\FileAsset;

/**
 * Class CssCompressor
 *
 * @package JBZoo\Assets\Filter
 */
class CssCompressor extends CompressorAbstract
{

    /**
     * Filter process.
     *
     * @return null|string
     * @throws Exception
     */
    public function process()
    {
        if (($this->_asset instanceof FileAsset)) {
            /** @var FileAsset $file */
            $file = $this->_asset;
            $full = $file->getFullPath();

            if (!FS::isFile($full)) {
                throw new Exception(sprintf('Asset file "%s" not found', $full));
            }

            return $this->_getFilePath($full);
        }

        return null;
    }

    /**
     * @param string $path
     * @return string
     */
    protected function _getFilePath($path)
    {
        /** @var FileAsset $file */
        $file   = $this->_asset;
        $ext    = $file->getExt();
        $params = $file->getParams();

        if ($ext === 'css') {
            $newFile = $this->_getResultFile();
            if ($params->get('minify_css', false) === true) {

                if ($this->_isExpired()) {
                    $styles = $file->getContent($path);
                    $styles = implode(PHP_EOL, [$this->_fileHead(), $this->_compress($styles)]);
                    file_put_contents($newFile, $styles);
                }

                return $newFile;
            }
        }

        return $path;
    }

    /**
     * CSS compressing.
     *
     * @param string $code
     * @return mixed|string
     */
    protected function _compress($code)
    {
        $code = (string) $code;

        // remove comments
        $code = preg_replace('#/\*[^*]*\*+([^/][^*]*\*+)*/#ius', '', $code);
        $code = str_replace(
            array("\r\n", "\r", "\n", "\t", '  ', '    ', ' {', '{ ', ' }', '; ', ';;', ';;;', ';;;;', ';}'),
            array('', '', '', '', '', '', '{', '{', '}', ';', ';', ';', ';', '}'),
            $code
        ); // remove tabs, spaces, newlines, etc.

        // remove spaces after and before colons
        $code = preg_replace('#([a-z\-])(:\s*|\s*:\s*|\s*:)#ius', '$1:', $code);

        // spaces before "!important"
        $code = preg_replace('#(\s*\!important)#ius', '!important', $code);

        // trim
        $code = Str::trim($code);

        return $code;
    }
}
