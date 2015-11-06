<?php
/**
 * Date: 15/11/06
 * Time: 17:24.
 */

namespace Remila\Image;


class ImageCompositorTest extends \PHPUnit_Framework_TestCase
{
    private $target = null;
    private $baseImage = null;
    private $parts = null;

    public function setUp()
    {
        $this->baseImage = \Phake::mock('\Remila\Image\Image');
        $this->parts = \Phake::mock('\Remila\Image\BaseImageParts');
        $this->target = new ImageCompositor($this->baseImage, array($this->parts));
    }

    public function testExecute()
    {
        $this->target->execute();
        \Phake::verify($this->parts)->execute($this->baseImage);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidArgumentEmptyArray()
    {
        new ImageCompositor($this->baseImage, array());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidArgumentInvalidArray()
    {
        new ImageCompositor($this->baseImage, array(1));
    }
}

