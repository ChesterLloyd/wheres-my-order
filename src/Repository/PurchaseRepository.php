<?php

namespace App\Repository;

use App\Entity\Purchase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Purchase>
 */
class PurchaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchase::class);
    }

    /**
     * @return Purchase[] Returns an array of purchases that are
     * ongoing for a specific user.
     */
    public function findRecentOngoingPurchasesByUserId(int $userId): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.status IN (:statuses)')
            ->andWhere('p.user = :user')
            ->setParameter('statuses', [
                Purchase::STATUS_ACKNOWLEDGED,
                Purchase::STATUS_DISPATCHED,
                Purchase::STATUS_OUT_FOR_DELIVERY,
            ])
            ->setParameter('user', $userId)
            ->orderBy('p.orderDate', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Purchase[] Returns an array of purchases that have
     * completed for a specific user.
     */
    public function findRecentCompletedPurchasesByUserId(int $userId): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.status IN (:statuses)')
            ->andWhere('p.user = :user')
            ->setParameter('statuses', [
                Purchase::STATUS_CANCELLED,
                Purchase::STATUS_DELIVERED,
            ])
            ->setParameter('user', $userId)
            ->orderBy('p.orderDate', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult()
        ;
    }
}
