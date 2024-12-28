<?php

namespace App\Repository;

use App\Entity\Tournee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tournee>
 *
 * @method Tournee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tournee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tournee[]    findAll()
 * @method Tournee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TourneeRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Tournee::class);
	}

	/**
	 * Retourne toutes les tournées associées à un commercial spécifique.
	 *
	 * @param int $commercialId
	 * @return Tournee[]
	 */
	public function findByCommercial(int $commercialId): array
	{
		return $this->createQueryBuilder('t')
			->andWhere('t.commercial = :commercialId')
			->setParameter('commercialId', $commercialId)
			->orderBy('t.id', 'ASC')
			->getQuery()
			->getResult();
	}

	/**
	 * Retourne toutes les tournées non terminées (incomplètes).
	 *
	 * @return Tournee[]
	 */
	public function findIncompleteTournees(): array
	{
		return $this->createQueryBuilder('t')
			->andWhere('t.completed = false')
			->orderBy('t.id', 'ASC')
			->getQuery()
			->getResult();
	}

	/**
	 * Retourne toutes les tournées associées à une zone spécifique.
	 *
	 * @param string $zoneCodePostal
	 * @return Tournee[]
	 */
	public function findByZone(string $zoneCodePostal): array
	{
		return $this->createQueryBuilder('t')
			->join('t.zone', 'z')
			->andWhere('z.codePostal = :codePostal')
			->setParameter('codePostal', $zoneCodePostal)
			->orderBy('t.id', 'ASC')
			->getQuery()
			->getResult();
	}
}
