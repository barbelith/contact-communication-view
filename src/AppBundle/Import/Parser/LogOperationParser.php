<?php


namespace AppBundle\Import\Parser;


use AppBundle\Entity\Operation;
use AppBundle\Import\Mapping\FieldDefinition;
use AppBundle\Import\Mapping\LogOperationMapping;
use AppBundle\Import\Mapping\MappingInterface;

class LogOperationParser implements ParserInterface
{
    /**
     * @var MappingInterface
     */
    private $mapping;

    /**
     * ParserInterface constructor.
     * @param MappingInterface $mapping
     */
    public function __construct(MappingInterface $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * @param string $item
     * @return bool
     */
    public function isItemValid($item)
    {
        if (0 === strlen($item)) {
            return false;
        }

        if ($this->isTypeUnknown($item)) {
            return true;
        }

        foreach ($this->mapping->getFieldDefinitions() as $fieldDefinition) {
            $value = $this->getFieldValue($fieldDefinition, $item);

            if (!$this->isFieldValid($fieldDefinition, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $item
     * @return ParsedOperation|null
     */
    public function parseItem($item)
    {
        if ($this->isTypeUnknown($item)) {
            return null;
        }

        return $this->createOperationFromItem($item);
    }

    /**
     * @param FieldDefinition $fieldDefinition
     * @param string $value
     * @return bool
     */
    private function isFieldValid(FieldDefinition $fieldDefinition, $value)
    {
        $length = strlen($value);

        if (!$fieldDefinition->isRequired() && 0 === $length) {
            return true;
        }

        if ($length < $fieldDefinition->getMinLength() || $length > $fieldDefinition->getMaxLength()) {
            return false;
        }

        return true;
    }

    /**
     * @param FieldDefinition $fieldDefinition
     * @param string $item
     * @return string
     */
    private function getFieldValue(FieldDefinition $fieldDefinition, $item)
    {
        $startPosition = $this->getStartPositionForFieldDefinition($fieldDefinition);

        return trim(substr($item, $startPosition, $fieldDefinition->getMaxLength()));
    }

    /**
     * @param FieldDefinition $fieldDefinition
     * @return int
     */
    private function getStartPositionForFieldDefinition(FieldDefinition $fieldDefinition)
    {
        $startPosition = 0;

        foreach ($this->mapping->getFieldDefinitions() as $existingDefinition) {
            if ($fieldDefinition->getName() === $existingDefinition->getName()) {
                return $startPosition;
            }

            $startPosition += $existingDefinition->getMaxLength();
        }
    }

    /**
     * @param $item
     * @return bool
     */
    private function isTypeUnknown($item)
    {
        $fieldDefinition = $this->mapping->getFieldDefinitions()->get(LogOperationMapping::FIELD_OPERATION_TYPE);

        return LogOperationMapping::OPERATION_TYPE_UNKNOWN === $this->getFieldValue($fieldDefinition, $item);
    }

    /**
     * @param $item
     * @return ParsedOperation
     */
    private function createOperationFromItem($item)
    {
        $operation = new ParsedOperation();

        $type = $this->getFieldValue(
            $this->mapping->getFieldDefinitions()->get(LogOperationMapping::FIELD_OPERATION_TYPE),
            $item
        );

        $operation->setType(
            $type === LogOperationMapping::OPERATION_TYPE_CALL ? Operation::TYPE_CALL : Operation::TYPE_SMS
        );

        $direction = $this->getFieldValue(
            $this->mapping->getFieldDefinitions()->get(LogOperationMapping::FIELD_OPERATION_DIRECTION),
            $item
        );

        $originNumber = $this->getFieldValue(
            $this->mapping->getFieldDefinitions()->get(LogOperationMapping::FIELD_OPERATION_ORIGIN),
            $item
        );

        $destinationNumber = $this->getFieldValue(
            $this->mapping->getFieldDefinitions()->get(LogOperationMapping::FIELD_OPERATION_DESTINATION),
            $item
        );

        if (LogOperationMapping::OPERATION_DIRECTION_INCOMING === $direction) {
            $operation->setDirection(Operation::DIRECTION_INCOMING);
            $operation->setPhoneOwner($destinationNumber);
            $operation->setContactNumber($originNumber);
        }

        if (LogOperationMapping::OPERATION_DIRECTION_OUTGOING === $direction) {
            $operation->setDirection(Operation::DIRECTION_OUTGOING);
            $operation->setPhoneOwner($originNumber);
            $operation->setContactNumber($destinationNumber);
        }

        $contactName = $this->getFieldValue(
            $this->mapping->getFieldDefinitions()->get(LogOperationMapping::FIELD_OPERATION_CONTACT_NAME),
            $item
        );
        $operation->setContactName($contactName);

        $date = $this->getFieldValue(
            $this->mapping->getFieldDefinitions()->get(LogOperationMapping::FIELD_OPERATION_DATE),
            $item
        );

        $operation->setDate(\DateTime::createFromFormat('dmYHis', $date));

        if (LogOperationMapping::OPERATION_TYPE_CALL === $type) {
            $duration = $this->getFieldValue(
                $this->mapping->getFieldDefinitions()->get(LogOperationMapping::FIELD_OPERATION_DURATION),
                $item
            );

            $operation->setDuration((int)$duration);
        }


        return $operation;
    }
}