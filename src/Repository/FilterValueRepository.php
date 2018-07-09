<?php

namespace App\Repository;

use App\Entity\FilterValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FilterValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method FilterValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method FilterValue[]    findAll()
 * @method FilterValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilterValueRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FilterValue::class);
    }

//    /**
//     * @return FilterValue[] Returns an array of FilterValue objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FilterValue
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
