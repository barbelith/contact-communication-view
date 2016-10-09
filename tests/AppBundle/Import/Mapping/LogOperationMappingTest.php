<?php


namespace AppBundle\Import\Mapping;


use Doctrine\Common\Collections\ArrayCollection;

class LogOperationMappingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getFieldsDefinitionReturnsArrayCollection()
    {
        $mapping = new LogOperationMapping();

        $this->assertInstanceOf(ArrayCollection::class, $mapping->getFieldDefinitions());
    }
    /**
     * @test
     * @dataProvider providerCheckFieldDefinitions
     */
    public function checkFieldDefinitions($field, $required, $length, $validOptions = null, $maxLength = null)
    {
        if (!$maxLength) {
            $maxLength = $length;
        }

        $mapping = new LogOperationMapping();

        /** @var FieldDefinition $definition */
        $definition = $mapping->getFieldDefinitions()->get($field);

        $this->assertEquals($field, $definition->getName());
        $this->assertEquals($length, $definition->getMinLength());
        $this->assertEquals($maxLength, $definition->getMaxLength());
        $this->assertEquals($validOptions, $definition->getValidOptions());
        $this->assertEquals($required, $definition->isRequired());
    }

    public function providerCheckFieldDefinitions()
    {
        return [
            [LogOperationMapping::FIELD_OPERATION_TYPE, true, 1, ['C', 'S', 'U']],
            [LogOperationMapping::FIELD_OPERATION_ORIGIN, true, 4, null, 9],
            [LogOperationMapping::FIELD_OPERATION_DESTINATION, true, 4, null, 9],
            [LogOperationMapping::FIELD_OPERATION_DIRECTION, true, 1, ['0', '1']],
            [LogOperationMapping::FIELD_OPERATION_CONTACT_NAME, true, 0, null, 24],
            [LogOperationMapping::FIELD_OPERATION_DATE, true, 14],
            [LogOperationMapping::FIELD_OPERATION_DURATION, false, 6],
        ];
    }
}
