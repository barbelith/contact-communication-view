<?php

namespace AppBundle\Import\Mapping;


use Doctrine\Common\Collections\ArrayCollection;

class LogOperationMapping implements MappingInterface
{
    const FIELD_OPERATION_TYPE                  = 'operation_type';
    const FIELD_OPERATION_ORIGIN                = 'origin';
    const FIELD_OPERATION_DESTINATION           = 'destination';
    const FIELD_OPERATION_DIRECTION             = 'direction';
    const FIELD_OPERATION_CONTACT_NAME          = 'contact_name';
    const FIELD_OPERATION_DATE                  = 'date';
    const FIELD_OPERATION_DURATION              = 'duration';

    const OPERATION_TYPE_CALL       = 'C';
    const OPERATION_TYPE_SMS        = 'S';
    const OPERATION_TYPE_UNKNOWN    = 'U';

    const OPERATION_DIRECTION_INCOMING    = '1';
    const OPERATION_DIRECTION_OUTGOING    = '0';

    protected $fieldDefinitions;

    /**
     * LogOperationMapping constructor.
     */
    public function __construct()
    {
        $this->fieldDefinitions = new ArrayCollection();
        $this->loadDefinitions();
    }

    /**
     * @return FieldDefinition[]|ArrayCollection
     */
    public function getFieldDefinitions()
    {
        return $this->fieldDefinitions;
    }

    private function loadDefinitions()
    {
        $this->setFieldDefinition(
            self::FIELD_OPERATION_TYPE,
            [
                'length'        => 1,
                'valid_options' => [self::OPERATION_TYPE_CALL, self::OPERATION_TYPE_SMS, self::OPERATION_TYPE_UNKNOWN]
            ]
        );

        $this->setFieldDefinition(
            self::FIELD_OPERATION_ORIGIN,
            [
                'min_length'        => 4,
                'max_length'        => 9,
            ]
        );

        $this->setFieldDefinition(
            self::FIELD_OPERATION_DESTINATION,
            [
                'min_length'        => 4,
                'max_length'        => 9,
            ]
        );

        $this->setFieldDefinition(
            self::FIELD_OPERATION_DIRECTION,
            [
                'length'        => 1,
                'valid_options' => [self::OPERATION_DIRECTION_OUTGOING, self::OPERATION_DIRECTION_INCOMING]
            ]
        );

        $this->setFieldDefinition(
            self::FIELD_OPERATION_CONTACT_NAME,
            [
                'min_length'        => 0,
                'max_length'        => 24,
            ]
        );

        $this->setFieldDefinition(
            self::FIELD_OPERATION_DATE,
            [
                'length'        => 14,
            ]
        );

        $this->setFieldDefinition(
            self::FIELD_OPERATION_DURATION,
            [
                'length'        => 6,
                'required'      => false
            ]
        );
    }

    /**
     * @param $name
     * @param array $options
     */
    private function setFieldDefinition($name, $options)
    {
        $options = array_merge(
            [
                'required'      => true,
                'valid_options' => null
            ],
            $options
        );

        if (!isset($options['min_length'])) {
            $options['min_length'] = $options['length'];
        }

        if (!isset($options['max_length'])) {
            $options['max_length'] = $options['min_length'];
        }

        $this->fieldDefinitions->set(
            $name,
            $this->createDefinition($name, $options['required'], $options['min_length'], $options['max_length'], $options['valid_options'])
        );
    }

    /**
     * @param $name
     * @param $required
     * @param $minLength
     * @param $maxLength
     * @param $validOptions
     * @return FieldDefinition
     */
    private function createDefinition($name, $required, $minLength, $maxLength, $validOptions)
    {
        $definition = new FieldDefinition();
        $definition->setName($name);
        $definition->setRequired($required);
        $definition->setMinLength($minLength);
        $definition->setMaxLength($maxLength);
        $definition->setValidOptions($validOptions);

        return $definition;
    }
}