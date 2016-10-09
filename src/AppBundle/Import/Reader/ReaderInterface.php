<?php


namespace AppBundle\Import\Reader;


use AppBundle\Import\Loader\LoaderInterface;
use AppBundle\Import\Parser\ParsedOperation;
use Doctrine\Common\Collections\ArrayCollection;

interface ReaderInterface
{
    /**
     * ReaderInterface constructor.
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader);

    /**
     * @return ParsedOperation[]|ArrayCollection
     */
    public function getOperations();
}