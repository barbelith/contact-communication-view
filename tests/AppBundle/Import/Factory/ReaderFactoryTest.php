<?php


namespace AppBundle\Import\Factory;


use AppBundle\Import\Loader\FileLoader;
use AppBundle\Import\Loader\HttpLoader;
use AppBundle\Import\Reader\LogReader;

class ReaderFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function createReaderUrl()
    {
        $factory = new ReaderFactory();

        $reader = $factory->getReader('http://www.example.com/example.log');

        $this->assertInstanceOf(LogReader::class, $reader);
        $this->assertInstanceOf(HttpLoader::class, $reader->getLoader());
    }

    /**
     * @test
     */
    public function createReaderPath()
    {
        $factory = new ReaderFactory();

        $reader = $factory->getReader('example.log');

        $this->assertInstanceOf(LogReader::class, $reader);
        $this->assertInstanceOf(FileLoader::class, $reader->getLoader());
    }
}
