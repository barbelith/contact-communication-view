<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Contact;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class ContactRepository extends EntityRepository
{
    /**
     * @param $ownerId
     * @return Contact[]|array
     */
    public function findContactsForOwner($ownerId)
    {
        return $this->findBy([
            'phone_owner_id' => $ownerId
        ]);
    }

    /**
     * @param $ownerId
     * @return Contact[]|array
     */
    public function findContactsForOwnerSortedByContactName($ownerId)
    {
        $qb = $this->createQueryBuilder('contact');

        $qb->where($qb->expr()->eq('contact.phone_owner_id', ':phone_owner_id'))
            ->setParameter(':phone_owner_id', $ownerId)
            ->addOrderBy('contact.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
}