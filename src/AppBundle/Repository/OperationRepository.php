<?php

namespace AppBundle\Repository;


use AppBundle\Entity\Operation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class OperationRepository extends EntityRepository
{
    /**
     * @param $ownerId
     * @return Operation[]|array
     */
    public function findOperationsForOwner($ownerId)
    {
        return $this->findBy([
            'phone_owner_id' => $ownerId
        ]);
    }

    /**
     * @param $ownerId
     * @return Operation[]|array
     */
    public function findOperationsForOwnerSortedByContactNameAndDate($ownerId)
    {
        $qb = $this->createQueryBuilder('operation')
          ->innerJoin('operation.contact', 'contact');

        $qb->where($qb->expr()->eq('operation.phone_owner_id', ':phone_owner_id'))
            ->setParameter(':phone_owner_id', $ownerId)
            ->addOrderBy('contact.name', 'ASC')
            ->addOrderBy('operation.date', 'ASC');

        return $qb->getQuery()->getResult();
    }
}