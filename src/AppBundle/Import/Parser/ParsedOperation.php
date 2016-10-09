<?php


namespace AppBundle\Import\Parser;


class ParsedOperation
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $phone_owner;

    /**
     * @var string
     */
    protected $contact_number;

    /**
     * @var string
     */
    protected $direction;

    /**
     * @var string
     */
    protected $contact_name;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var int
     */
    protected $duration;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getPhoneOwner()
    {
        return $this->phone_owner;
    }

    /**
     * @param mixed $phone_owner
     */
    public function setPhoneOwner($phone_owner)
    {
        $this->phone_owner = $phone_owner;
    }

    /**
     * @return mixed
     */
    public function getContactNumber()
    {
        return $this->contact_number;
    }

    /**
     * @param mixed $contact_number
     */
    public function setContactNumber($contact_number)
    {
        $this->contact_number = $contact_number;
    }

    /**
     * @return mixed
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param mixed $direction
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
    }

    /**
     * @return mixed
     */
    public function getContactName()
    {
        return $this->contact_name;
    }

    /**
     * @param mixed $contact_name
     */
    public function setContactName($contact_name)
    {
        $this->contact_name = $contact_name;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }
}