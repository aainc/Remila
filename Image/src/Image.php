<?php
namespace Remila\Image;
use Remila\Image\TempFile;

/**
 * Simple \Imagick Wrapper
 * Class Image
 * @package Rmila\Image
 */
class Image
{
    private $file = null;
    private $innerImage = null;
    private $type = null;
    private $filter = null;
    private $format = null;
    const FORMAT_JPEG = 'jpeg';
    const FORMAT_PNG  = 'png';
    const FORMAT_BITMAP  = 'bmp';


    public function __construct($file = null, $format = Image::FORMAT_JPEG, $filter = null)
    {
        if (!$filter) $filter = \Imagick::FILTER_BLACKMAN;
        $this->filter = $filter;
        $this->format = $format;
        if (!$file) return;
        if (!($file instanceof TempFile)) {
            throw new \InvalidArgumentException ('wants instance of TempFile');
        }
        $this->file = $file;
    }

    /**
     * get TempFile
     * @return TempFile|null
     */
    public function getTempFile()
    {
        return $this->file;
    }

    /**
     * get Imagick
     * @return \Imagick|null
     * @throws \InvalidArgumentException
     */
    public function getObject()
    {
        if (!$this->innerImage) {
            if (!$this->file) {
                throw new \InvalidArgumentException ('no file');
            }
            $this->innerImage = new \Imagick ($this->file->getPath());
        }
        return $this->innerImage;
    }

    /**
     * set \Imagick
     * @param $param
     */
    public function setObject($param)
    {
        $this->innerImage = $param;
    }

    /**
     * bye \Imagick
     */
    public function clearObject()
    {
        $this->innerImage = null;
    }

    /**
     * get width
     * @return mixed
     */
    public function getImageWidth()
    {
        return $this->getObject()->getImageWidth();
    }

    /**
     * get heigth
     * @return mixed
     */
    public function getImageHeight()
    {
        return $this->getObject()->getImageHeight();
    }

    /**
     * create new TempFile
     * @param null $out
     * @return TempFile
     */
    public function out($out = null)
    {
        if (!$out) $out = '/tmp/' . uniqid();
        $this->getObject()->setformat($this->format);
        if ($this->filter === 'jpg') {
            $this->getObject()->setcompression(imagick::COMPRESSION_JPEG);
            $this->getObject()->setimagecompressionquality(80);
        }
        $this->getObject()->writeImages($out, true);
        return new TempFile ($out, null, $this->getType());
    }

    /**
     * justify image
     * @param $width
     * @param $height
     * @param int $marginLeft
     * @param int $marginRight
     * @param int $marginTop
     * @param int $marginBottom
     * @return $this resized image
     */
    public function justify(
        $width,
        $height,
        $marginLeft = 0,
        $marginRight = 0,
        $marginTop = 0,
        $marginBottom = 0) {

        $imgBase = $this->getObject();
        $marginWidth = $marginLeft + $marginRight;
        $marginHeight = $marginTop + $marginBottom;

        if ($imgBase->getImageWidth() > $imgBase->getImageHeight()) {
            $imgBase->resizeImage($width - $marginWidth, 0, $this->filter, 0, true);
            if ($height - $marginHeight > $imgBase->getImageHeight()) {
                $size = ($height - $marginHeight - $imgBase->getImageHeight()) / 2;
                $imgBase->spliceImage(0, $size, 0, 0);
                $imgBase->spliceImage(0, $size, $imgBase->getImageWidth(), $imgBase->getImageHeight());
            } else {
                // 横長だけどアスペクト比的にフレームの枠よりも縦長
                $imgBase->resizeImage(0, $height - $marginHeight, $this->filter, 0, true);
                $size = ($width - $marginWidth - $imgBase->getImageWidth()) / 2;
                $imgBase->spliceImage($size, 0, 0, 0);
                $imgBase->spliceImage($size, 0, $imgBase->getImageWidth(), $imgBase->getImageHeight());
            }
        } else {
            $imgBase->resizeImage(0, $height - $marginHeight, $this->filter, 0, true);
            if ($width - $marginWidth > $imgBase->getImageWidth()) {
                $size = ($width - $marginWidth - $imgBase->getImageWidth()) / 2;
                $imgBase->spliceImage($size, 0, 0, 0);
                $imgBase->spliceImage($size, 0, $imgBase->getImageWidth(), $imgBase->getImageHeight());
            } else {
                // 縦長だけどアスペクト比的にフレームの枠よりも横長
                $imgBase->resizeImage($width - $marginWidth, 0, $this->filter, 0, true);
                $size = ($height - $marginHeight - $imgBase->getImageHeight()) / 2;
                $imgBase->spliceImage(0, $size, 0, 0);
                $imgBase->spliceImage(0, $size, $imgBase->getImageWidth(), $imgBase->getImageHeight());
            }
        }

        if ($marginLeft || $marginTop) {
            $imgBase->spliceImage($marginLeft, $marginTop, 0, 0);
        }
        if ($marginRight || $marginBottom) {
            $imgBase->spliceImage($marginRight, $marginBottom, $imgBase->getImageWidth(), $imgBase->getImageHeight());
        }
        $this->innerImage = $imgBase;
        return $this;
    }

    /**
     * resize
     * @param $size
     * @return $this
     */
    public function resize($size)
    {
        $imgBase = $this->getObject();
        if ($imgBase->getImageWidth() > $imgBase->getImageHeight()) {
            $imgBase->resizeImage($size, 0, $this->filter, 0);
        } else {
            $imgBase->resizeImage(0, $size, $this->filter, 0);
        }
        $this->innerImage = $imgBase;
        return $this;
    }

    /**
     * get fileType
     * @param $path
     * @return bool|null|string fileType
     */
    public function getType($path = null)
    {
        if ($this->type) return $this->type;
        if (!$path) $path = $this->file->getPath();
        $f = fopen($path, 'r');
        $data = fread($f, 8);
        fclose($f);
        if (preg_match('#^\x89PNG#', $data))       return $this->type = 'image/png';
        elseif (preg_match('#^GIF#', $data))       return $this->type = 'image/gif';
        elseif (preg_match('#^\xFF\xD8#', $data))  return $this->type = 'image/jpeg';
        else                                       return '';
    }
}
