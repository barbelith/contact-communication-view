<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OperationRepository")
 * @ORM\Table(
 *  indexes={
 *     @Index(name="phone_owner_contact_idx", columns={"phone_owner_id", "contact_id"}
 *  }
 * )
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="type", type="string")
 * @DiscriminatorMap({"call" = "CallOperation", "sms" = "SMSOperation"})
 */
class Operation
{
    const TYPE_CALL = 'call';
    const TYPE_SMS = 'sms';

    const DIRECTION_INCOMING = 'I';
    const DIRECTION_OUTGOING = 'O';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $type;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $phone_owner_id;

    /**
     * @ORM\Column(type="integer")
     * @ManyToOne(targetEntity="Contact")
     * @JoinColumn(name="contact_id", referencedColumnName="id")
     * @var Contact
     */
    protected $contact;

    /**
     * @ORM\Column(type="string", length=1)
     * @var string
     */
    protected $direction;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $date;

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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param Contact $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param string $direction
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
    }

    /**
     * @return \DateTime
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param \DateTime $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}