<?php
namespace Remila\Image;
class TempFileTest extends \PHPUnit_Framework_TestCase
{
    private $target = null;
    public function setUp()
    {
        $this->target = new TempFile();
    }

    public function testInstance ()
    {
        $this->assertInstanceOf('\Remila\Image\TempFile', $this->target);
    }
}
