<?php


namespace AppBundle\Import\Reader;


use AppBundle\Import\Loader\LoaderInterface;

class LogReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function loaderLoads()
    {
        $loader = $this->createMock(LoaderInterface::class);
        $loader->expects($this->once())->method('getContents')->willReturn([]);

        $reader = new LogReader($loader);

        $reader->getOperations();
    }

    /**
     * @test
     * @expectedException AppBundle\Import\Exception\NoContentsLoadedException
     */
    public function emptyContentThrowsException()
    {
        $loader = $this->createMock(LoaderInterface::class);
        $loader->method('getContents')->willReturn(null);

        $reader = new LogReader($loader);

        $reader->getOperations();
    }

    /**
     * @test
     */
    public function callIsParsed()
    {
        $loader = $this->createMock(LoaderInterface::class);
        $loader->method('getContents')->willReturn(
            ['C6112223336009998880Pepe                    01012016205203000142']
        );

        $reader = new LogReader($loader);

        $operations = $reader->getOperations();

        $this->assertCount(1, $operations);
    }
}
