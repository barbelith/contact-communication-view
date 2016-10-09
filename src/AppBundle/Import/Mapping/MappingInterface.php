<?php


namespace AppBundle\Import\Mapping;


use Doctrine\Common\Collections\ArrayCollection;

interface MappingInterface
{
    /**
     * @return FieldDefinition[]|ArrayCollection
     */
    public function getFieldDefinitions();
}