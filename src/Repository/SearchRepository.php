<?php

namespace App\Repository;

use App\Entity\Search;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\RegistryInterface;

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
}
