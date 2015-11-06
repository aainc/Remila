<?php
namespace Remila\Image;
/**
 * Class ImageCompositor
 */
class ImageCompositor
{
    private $base = null;
    private $parts = null;

    public function __construct(Image $base, array $parts)
    {
        $this->base = $base;
        if (!$parts || array_filter($parts, function($row){return !($row instanceof BaseImageParts);})){
            throw new \InvalidArgumentException('$parts is array of BaseImageParts');
        }
        $this->parts = $parts;
    }

    /**
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function execute()
    {
        foreach ($this->parts as $part) {
            $part->execute($this->base);
        }
        return $this->base->getObject();
    }

    public function executeOut($out = null)
    {
        $this->execute();
        return $this->base->out($out);
    }

    public function getBase()
    {
        return $this->base;
    }

    /**
     * @return array|null
     */
    public function getParts()
    {
        return $this->parts;
    }
}
