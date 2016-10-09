<?php


namespace AppBundle\Import\Mapping;


class FieldDefinition
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var boolean
     */
    protected $required;

    /**
     * @var int
     */
    protected $minLength;

    /**
     * @var int
     */
    protected $maxLength;

    /**
     * @var array
     */
    protected $validOptions;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @return int
     */
    public function getMinLength()
    {
        return $this->minLength;
    }

    /**
     * @param int $minLength
     */
    public function setMinLength($minLength)
    {
        $this->minLength = $minLength;
    }

    /**
     * @return int
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * @param int $maxLength
     */
    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;
    }

    /**
     * @return array
     */
    public function getValidOptions()
    {
        return $this->validOptions;
    }

    /**
     * @param array $validOptions
     */
    public function setValidOptions($validOptions)
    {
        $this->validOptions = $validOptions;
    }
}