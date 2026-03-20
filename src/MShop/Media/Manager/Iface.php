<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package MShop
 * @subpackage Media
 */
namespace Aimeos\M_Shop\Media\Manager;

use Psr\Http\Message\Uploaded_File_Interface;
/**
 * Generic interface for media managers.
 *
 * @package MShop
 * @subpackage Media
 */
interface Iface extends \Aimeos\M_Shop\Common\Manager\Iface
{
    /**
     * Copies the media item and the referenced files
     *
     * @param \Aimeos\MShop\Media\Item\Iface $item Media item whose files should be copied
     * @return \Aimeos\MShop\Media\Item\Iface Copied media item with new files
     */
    public function copy(\Aimeos\M_Shop\Media\Item\Iface $item): \Aimeos\M_Shop\Media\Item\Iface;
    /**
     * Rescales the original file to preview files referenced by the media item
     *
     * The height/width configuration for scaling
     * - mshop/media/<files|preview>/maxheight
     * - mshop/media/<files|preview>/maxwidth
     * - mshop/media/<files|preview>/force-size
     *
     * @param \Aimeos\MShop\Media\Item\Iface $item Media item whose files should be scaled
     * @param bool $force True to enforce creating new preview images
     * @return \Aimeos\MShop\Media\Item\Iface Rescaled media item
     */
    public function scale(\Aimeos\M_Shop\Media\Item\Iface $item, bool $force = false): \Aimeos\M_Shop\Media\Item\Iface;
    /**
     * Stores the uploaded file and returns the updated item
     *
     * @param \Aimeos\MShop\Media\Item\Iface $item Media item for storing the file meta data, "domain" must be set
     * @param \Psr\Http\Message\UploadedFileInterface $file Uploaded file object
     * @param \Psr\Http\Message\UploadedFileInterface|null $preview Uploaded preview image
     * @return \Aimeos\MShop\Media\Item\Iface Updated media item including file and preview paths
     */
    public function upload(\Aimeos\M_Shop\Media\Item\Iface $item, Uploaded_File_Interface $file, ?Uploaded_File_Interface $preview = null): \Aimeos\M_Shop\Media\Item\Iface;
}