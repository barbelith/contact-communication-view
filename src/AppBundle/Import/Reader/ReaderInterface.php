<?php


namespace AppBundle\Import\Reader;


use AppBundle\Entity\Operation;
use AppBundle\Import\Loader\LoaderInterface;
use Doctrine\Common\Collections\ArrayCollection;

interface ReaderInterface
{
    /**
     * ReaderInterface constructor.
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader);

    /**
     * @return Operation[]|ArrayCollection
     */
    public function getOperations();
}