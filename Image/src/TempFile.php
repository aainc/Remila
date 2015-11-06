<?php
namespace Remila\Image;
class TempFile
{
    protected $path = null;
    protected $tempDirectory = '/tmp';
    private  $originalName   = null;
    private  $fileType   = null;

    public function __construct($param = null, $originalName = null, $fileType = null)
    {
        $this->originalName = $originalName;
        $this->fileType = $fileType;
        if (is_null($param)) {
            $path = $this->tempDirectory . '/' . uniqid();
            $this->path = $path;
        } elseif ($data = @file_get_contents($param)) {
            $this->originalName = basename($param);
            $path = $this->tempDirectory . '/' . uniqid();
            file_put_contents($path, $data);
            $this->path = $path;
        } else {
            throw new \RuntimeException ('failed to copy file.' . $param);
        }
    }

    public function getPath()
    {
        return $this->path;
    }

    /**
     * this file save to path
     * @param $path
     * @param bool $force
     * @throws \RuntimeException
     */
    public function saveTo($path, $force = false)
    {
        if (!$force && is_file($path)) {
            throw new \RuntimeException ('already exists. ' . $path);
        }
        copy($this->path, $path);
        chmod($path, 0777);
    }

    /**
     * @param $path directory path
     * @param bool $force
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function saveAsOriginalName($path, $force = false)
    {
        if (!$this->originalName) {
            throw new \RuntimeException('originalName is not set');
        }
        if (is_dir($path)) {
            $path .=  strtr($this->originalName, DIRECTORY_SEPARATOR, '-');
            $this->saveTo($path, $force);
        } else {
            throw new \InvalidArgumentException($path . ' is not directory');
        }
    }

    /**
     *
     */
    public function __destruct()
    {
        try {
            if (is_dir($this->path)) {
                $this->removeDir($this->path);
            } elseif (is_file($this->path)) {
                @unlink($this->path);
            }
        } catch (\Exception $e){}
    }

    /**
     * @return string
     */
    public function getTempDirectory()
    {
        return $this->tempDirectory;
    }

    /**
     * @return null
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * remove directory recursive
     * @param string $dir path to directory
     */
    public function removeDir($dir) {

        $cnt = 0;
        $handle = opendir($dir);
        if (!$handle) {
            return ;
        }
        while ($item = readdir($handle)) {
            if ($item === "." || $item === "..") {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $cnt = $cnt + $this->removeDir($path);
            } else {
                unlink($path);
            }
        }
        closedir($handle);
        if (!rmdir($dir)) {
            return ;
        }
    }

    public function toImage ($fileType = Image::FORMAT_JPEG, $filter = null)
    {
        if ($filter === null) {
            $filter = \Imagick::FILTER_BLACKMAN;
        }
        return new Image($this, $fileType, $filter);
    }

    public function getType ()
    {
        if ((!$this->fileType || $this->fileType === 'application/octet-stream') && is_file($this->getPath())) {
            $path = $this->getPath();
            $f = fopen($path, 'r');
            $data = fread($f, 8);

            fclose($f);
            if (preg_match('#^\x89PNG#', $data))       $this->fileType = 'image/png';
            elseif (preg_match('#^GIF#', $data))       $this->fileType = 'image/gif';
            elseif (preg_match('#^\xFF\xD8#', $data))  $this->fileType = 'image/jpeg';
            else                                       $this->fileType = '';
        }
        return $this->fileType;
    }
}
