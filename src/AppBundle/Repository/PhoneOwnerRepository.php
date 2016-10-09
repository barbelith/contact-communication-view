<?php

namespace AppBundle\Repository;


use AppBundle\Entity\PhoneOwner;
use Doctrine\ORM\EntityRepository;

class PhoneOwnerRepository extends EntityRepository
{
    /**
     * @param $number
     * @return null|PhoneOwner
     */
    public function findOrCreate($number)
    {
        $phoneOwner = $this->findOneBy(['number' => $number]);

        if (!$phoneOwner) {
            $phoneOwner = new PhoneOwner();
            $phoneOwner->setNumber($number);

            $this->getEntityManager()->persist($phoneOwner);
            $this->getEntityManager()->flush($phoneOwner);
        }

        return $phoneOwner;
    }
}