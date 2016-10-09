<?php


namespace AppBundle\Import\Factory;


use AppBundle\Import\Loader\FileLoader;
use AppBundle\Import\Loader\HttpLoader;
use AppBundle\Import\Reader\LogReader;

class ReaderFactory
{
    /**
     * @param $path
     * @return LogReader
     */
    public function getReader($path)
    {
        if ($this->isUrl($path)) {
            $loader = new HttpLoader($path);
        }

        if (!isset($loader)) {
            $loader = new FileLoader($path);
        }

        return new LogReader($loader);
    }

    /**
     * @param $path
     * @return bool
     */
    private function isUrl($path)
    {
        return filter_var($path, FILTER_VALIDATE_URL) !== false;
    }
}