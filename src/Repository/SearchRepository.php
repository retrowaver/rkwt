<?php

namespace App\Repository;

use App\Entity\Search;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Search|null find($id, $lockMode = null, $lockVersion = null)
 * @method Search|null findOneBy(array $criteria, array $orderBy = null)
 * @method Search[]    findAll()
 * @method Search[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Search::class);
    }

    public function findByStatus(array $statuses): Collection
    {
        return new ArrayCollection(
            $this->createQueryBuilder('s')
                ->andWhere('s.status IN (:statuses)')
                ->setParameter('statuses', $statuses)
                ->getQuery()
                ->getResult()
        );
    }

//    /**
//     * @return Search[] Returns an array of Search objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Search
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
