<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContactRepository")
 * @ORM\Table(
 *  uniqueConstraints={
 *     @ORM\UniqueConstraint(name="phone_owner_phone_number_idx", columns={"phone_owner_id", "number"})
 *  },
 *  indexes={
 *     @ORM\Index(name="phone_owner_idx", columns={"phone_owner_id"})
 *  }
 * )
 */
class Contact
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $phone_owner_id;

    /**
     * @ORM\Column(type="string", length=9, unique=true)
     */
    protected $number;

    /**
     * @ORM\Column(type="string", length=24, unique=true)
     */
    protected $name;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getPhoneOwnerId()
    {
        return $this->phone_owner_id;
    }

    /**
     * @param int $phone_owner_id
     */
    public function setPhoneOwnerId($phone_owner_id)
    {
        $this->phone_owner_id = $phone_owner_id;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}