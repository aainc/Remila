<?php
/**
 * Date: 15/11/06
 * Time: 12:21.
 */

namespace Remila\Image\strategy;


use Remila\Image\BaseImageParts;
use Remila\Image\Image;

class Composer extends BaseImageParts
{

    private $partImage = null;
    public function __construct($x, $y, Image $partImage) {
        parent::__construct($x, $y);
        $this->partImage = $partImage;
    }
    public function execute(Image $image)
    {
        $partObject = $this->partImage->getObject();
        $image->getObject()->compositeImage($partObject, $partObject->getImageCompose(), $this->getX(), $this->getY());
    }
}