<?php


namespace AppBundle\Entity;


class CallOperation extends Operation
{
    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $duration;

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