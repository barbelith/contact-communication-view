<?php


namespace AppBundle\Import\Loader;


class FileLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \LogicException
     */
    public function getContentsWithoutFile()
    {
        $loader = new FileLoader('foo');

        $loader->getContents();
    }

    /**
     * @test
     */
    public function getContentsOfFile()
    {
        $loader = new FileLoader(__DIR__.'/../../Resources/communications.log');

        $contents = $loader->getContents();

        $this->assertTrue(is_array($contents));
        $this->assertGreaterThan(0, count($contents));
    }

    /**
     * @test
     */
    public function getContentsOfFileWithEmptyLines()
    {
        $loader = new FileLoader(__DIR__.'/../../Resources/empty-lines.log');

        $this->assertEquals(0, count($loader->getContents()));
    }
}
