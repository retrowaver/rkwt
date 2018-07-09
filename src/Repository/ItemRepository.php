<?php

namespace App\Repository;

use App\Entity\Item;
use App\Entity\Search;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Item::class);
    }

//    /**
//     * @return Item[] Returns an array of Item objects
//     */
    
    public function findByUserId(int $userId): Collection
    {
        $queryBuilder = $this ->createQueryBuilder('my_repository');

        return new ArrayCollection(
            $queryBuilder
                ->where(
                    $queryBuilder->expr()->in(
                        'my_repository.search',
                        $this
                            ->createQueryBuilder('subquery_repository')
                            ->select('s.id')
                            ->from(Search::class, 's')
                            ->where('s.user = :user')
                            ->getDQL()
                    )
                )
                ->setParameter(':user', $userId)
                ->getQuery()
                ->getResult()
        );
    }
    

    /*
    public function findOneBySomeField($value): ?Item
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
