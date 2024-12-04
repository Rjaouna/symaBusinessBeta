<?php

namespace App\Repository;

use App\Entity\Limitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Limitation>
 */
class LimitationRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Limitation::class);
	}
	// src/Repository/LimitationRepository.php

	// src/Repository/LimitationRepository.php

	public function findLimitationWithUsage($typeCarte, $usageConsomation)
	{
		return $this->createQueryBuilder('l')
			->where('l.type_carte = :typeCarte')
			->andWhere('l.limite < :usageConsomation')
			->setParameter('typeCarte', $typeCarte)
			->setParameter('usageConsomation', $usageConsomation)
			->orderBy('l.id', 'ASC') // Ordre croissant basé sur l'ID pour obtenir la première limite
			->setMaxResults(1) // Limiter les résultats à un seul élément
			->getQuery()
			->getOneOrNullResult(); // Renvoie une seule limitation ou null
	}



	//    /**
	//     * @return Limitation[] Returns an array of Limitation objects
	//     */
	//    public function findByExampleField($value): array
	//    {
	//        return $this->createQueryBuilder('b')
	//            ->andWhere('b.exampleField = :val')
	//            ->setParameter('val', $value)
	//            ->orderBy('b.id', 'ASC')
	//            ->setMaxResults(10)
	//            ->getQuery()
	//            ->getResult()
	//        ;
	//    }

	//    public function findOneBySomeField($value): ?Limitation
	//    {
	//        return $this->createQueryBuilder('b')
	//            ->andWhere('b.exampleField = :val')
	//            ->setParameter('val', $value)
	//            ->getQuery()
	//            ->getOneOrNullResult()
	//        ;
	//    }
}
