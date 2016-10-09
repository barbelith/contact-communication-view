<?php


namespace AppBundle\Import\Loader;


interface LoaderInterface
{
    /**
     * @return array
     */
    public function getContents();
}