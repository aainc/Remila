<?php
/**
 * Date: 15/11/06
 * Time: 12:18.
 */

namespace Remila\Image;


abstract class BaseImageParts
{
    private $x = 0;
    private $y = 0;
    public function __construct ($x,$y) {
        $this->x = $x;
        $this->y = $y;
    }

    abstract public function execute(Image $image);

    /**
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }
}