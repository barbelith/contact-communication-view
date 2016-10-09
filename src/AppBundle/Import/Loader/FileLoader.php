<?php


namespace AppBundle\Import\Loader;


class FileLoader implements LoaderInterface
{
    protected $path;

    /**
     * FileLoader constructor.
     * @param $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }


    /**
     * @return array
     */
    public function getContents()
    {
        if (!is_file($this->path)) {
            throw new \LogicException(sprintf('The file %s does not exist', $this->path));
        }

        $handle = fopen($this->path, 'r');

        if ($handle) {
            return $this->fileToArray($handle);
        }

        throw new \LogicException(sprintf('The file %s could not be read', $this->path));

    }

    /**
     * @param $handle
     * @return array
     */
    private function fileToArray($handle)
    {
        $contents = [];

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);

            if (strlen($line) > 0) {
                $contents[] = $line;
            }
        }

        fclose($handle);

        return $contents;
    }
}