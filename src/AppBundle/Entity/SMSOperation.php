<?php


namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class SMSOperation extends Operation
{
    public function getType()
    {
        return self::TYPE_SMS;
    }
}