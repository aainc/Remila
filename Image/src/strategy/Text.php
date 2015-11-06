<?php
namespace Remila\Image;
use ImagickDraw;

/**
 * Class ImageDraw
 * @package Remila\Image
 */
class Text extends BaseImageParts
{

    private $text = null;
    private $font = null;
    private $color = null;
    private $size = null;

    public function __construct($x, $y, $text, $font = null, $color = null, $size = null)
    {
        parent::__construct($x, $y);
        $this->text = $text;
        $this->font = $font;
        $this->color = $color;
        $this->size = $size;
    }


    public function execute(Image $image)
    {
        $draw = new ImagickDraw ();
        if ($this->font) $draw->setFont($this->font);
        if ($this->color) $draw->setFillColor($this->color);
        if ($this->size) $draw->setFontSize($this->size);
        // $draw->annotation($this->getX(), $this->getY(), $this->text);
        $image->getObject()->annotateImage($draw, $this->getX(), $this->getY(), 0, $this->getText());
    }

    /**
     * @return null
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @return null
     */
    public function getFont()
    {
        return $this->font;
    }

    /**
     * @return null
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return null
     */
    public function getText()
    {
        return $this->text;
    }
}
