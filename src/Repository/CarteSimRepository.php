<?php

namespace App\Repository;

use App\Entity\CarteSim;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CarteSim>
 */
class CarteSimRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarteSim::class);
    }
    public function countByType(): array
    {
        return $this->createQueryBuilder('c')
            ->select('t.nom as typeName, COUNT(c.id) as count')
            ->leftJoin('c.type', 't') // Utilisation de LEFT JOIN pour inclure les types même sans cartes réservées
            ->where('c.reserved = 0 OR c.reserved IS NULL') // Vérifier les cartes réservées à 0 ou non réservées
            ->groupBy('t.id')
            ->getQuery()
            ->getResult();
    }



//    /**
//     * @return CarteSim[] Returns an array of CarteSim objects
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

//    public function findOneBySomeField($value): ?CarteSim
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
