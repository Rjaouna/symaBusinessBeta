<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }
    public function findCommandesByClientAndMonth($clientId, $startDate, $endDate)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->andWhere('c.createdAt BETWEEN :start AND :end')
            ->andWhere('c.factured = :factured')
            ->setParameter('user', $clientId)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->setParameter('factured', false) // Factured est un booléen, donc false pour 0
            ->getQuery()
            ->getResult();
    }

    public function findTotalCommandeByClient($clientId, $startDate, $endDate)
    {
        return $this->createQueryBuilder('c')
            ->select('SUM(c.montantHt) as total')
            ->andWhere('c.user = :user')
            ->andWhere('c.createdAt BETWEEN :start AND :end')
            ->setParameter('user', $clientId)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getSingleScalarResult();
    }


    //    /**
    //     * @return Commande[] Returns an array of Commande objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Commande
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
