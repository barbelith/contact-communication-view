<?php


namespace AppBundle\Import\Loader;


class HttpLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \LogicException
     */
    public function invalidUrl()
    {
        $loader = new HttpLoader('foo');

        $loader->getContents();
    }

    /**
     * @test
     */
    public function downloadedFileIsParsed()
    {
        $file = <<<EOF
First line
Second line
EOF;
        $expected = [
            'First line',
            'Second line'
        ];

        $loader = $this->getMockBuilder(HttpLoader::class)
            ->setConstructorArgs(['http://example.com'])
            ->setMethods(['downloadFile'])
            ->getMock();

        $loader->method('downloadFile')->willReturn($file);


        $this->assertEquals($expected, $loader->getContents());
    }

    /**
     * @test
     */
    public function downloadedFileIsTrimmed()
    {
        $file = <<<EOF
First line  
Second line
  
EOF;
        $expected = [
            'First line',
            'Second line'
        ];

        $loader = $this->getMockBuilder(HttpLoader::class)
            ->setConstructorArgs(['http://example.com'])
            ->setMethods(['downloadFile'])
            ->getMock();

        $loader->method('downloadFile')->willReturn($file);


        $this->assertEquals($expected, $loader->getContents());
    }
}