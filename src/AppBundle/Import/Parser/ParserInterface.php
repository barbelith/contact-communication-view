<?php


namespace AppBundle\Import\Parser;


use AppBundle\Import\Mapping\MappingInterface;

interface ParserInterface
{
    /**
     * ParserInterface constructor.
     * @param MappingInterface $mapping
     */
    public function __construct(MappingInterface $mapping);

    /**
     * @param mixed $item
     * @return bool
     */
    public function isItemValid($item);

    /**
     * @param $item
     * @return ParsedOperation
     */
    public function parseItem($item);
}