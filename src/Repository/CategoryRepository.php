<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Category::class);
    }
    
    public function findByCatParent(int $catParent)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.catParent = :val')
            ->setParameter('val', $catParent)
            ->orderBy('c.catPosition', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByCatId(int $catId)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.catId = :val')
            ->setParameter('val', $catId)
            ->getQuery()
            ->getResult()
        ;
    }
}
