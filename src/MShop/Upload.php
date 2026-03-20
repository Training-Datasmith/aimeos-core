<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 * @package MShop
 */
namespace Aimeos\M_Shop;

use Psr\Http\Message\Uploaded_File_Interface;
/**
 * Upload trait
 *
 * @package MShop
 */
trait Upload
{
    /**
     * Returns the context object.
     *
     * @return \Aimeos\MShop\ContextIface Context object
     */
    abstract protected function context(): \Aimeos\M_Shop\Context_Iface;
    /**
     * Deletes a file from the file system
     *
     * @param string $filepath Relative path to the file in the file system
     * @param string $fsname File system name
     * @return self Same object for fluent interface
     */
    protected function delete_file(string $filepath, string $fsname = 'fs-media'): self
    {
        $fs = $this->context()->fs($fsname);
        if ($filepath && $fs->has($filepath)) {
            $fs->rm($filepath);
        }
        return $this;
    }
    /**
     * Returns the file mime type for the uploaded file
     *
     * Caution: This method must be called before storeFile() to be able to
     * determine the file mime type!
     *
     * @param \Psr\Http\Message\UploadedFileInterface $file Uploaded file object
     * @return string File mime type
     */
    protected function mimetype(Uploaded_File_Interface $file): string
    {
        $stream = $file->get_stream();
        if (!$stream->is_seekable()) {
            return '';
        }
        $stream->rewind();
        $content = $stream->read(100);
        $stream->rewind();
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->buffer($content);
    }
    /**
     * Stores the uploaded file
     *
     * @param \Psr\Http\Message\UploadedFileInterface $file Uploaded file object
     * @param string $filepath Relative path to the file in the file system
     * @param string $fsname File system name
     * @return self Same object for fluent interface
     */
    protected function store_file(Uploaded_File_Interface $file, string $filepath, string $fsname = 'fs-media'): self
    {
        if (($code = $file->get_error()) !== UPLOAD_ERR_OK) {
            $errors = [0 => 'There is no error, the file uploaded with success', 1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini', 2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', 3 => 'The uploaded file was only partially uploaded', 4 => 'No file was uploaded', 6 => 'Missing a temporary folder', 7 => 'Failed to write file to disk.', 8 => 'A PHP extension stopped the file upload.'];
            throw new \RuntimeException($errors[$code] ?? sprintf('An unknown error occured with code %1$d', $code));
        }
        $fs = $this->context()->fs($fsname);
        if ($fs instanceof \Aimeos\Base\Filesystem\Dir_Iface && !$fs->has($dirname = dirname($filepath))) {
            $fs->mkdir($dirname);
        }
        $fs->writes($filepath, $file->get_stream()->detach());
        return $this;
    }
}