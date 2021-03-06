<?php

namespace App\Repository;

use App\Entity\Item;
use App\Entity\Search;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class ItemRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Item::class);
    }
    
    public function findLatest(int $userId, array $statuses = [], int $page = 1): Pagerfanta
    {
        // Get items that belong to searches of specified user
        $queryBuilder = $this ->createQueryBuilder('i');
        $queryBuilder
            ->where(
                $queryBuilder->expr()->in(
                    'i.search',
                    $this
                        ->createQueryBuilder('subquery_repository')
                        ->select('s.id')
                        ->from(Search::class, 's')
                        ->where('s.user = :user')
                        ->getDQL()
                )
            )
            ->orderBy('i.id', 'DESC')
            ->setParameter(':user', $userId);

        if (!empty($statuses)) {
            $queryBuilder->andWhere("i.status IN(:statuses)")
                ->setParameter(':statuses', $statuses);
        }

        $query = $queryBuilder->getQuery();
        return $this->createPaginator($query, $page);
    }

    public function findByStatus(int $status): Collection
    {
        return new ArrayCollection(
            $this->createQueryBuilder('i')
                ->andWhere('i.status = :status')
                ->setParameter('status', $status)
                ->getQuery()
                ->getResult()
        );
    }

    private function createPaginator(Query $query, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(Item::MAX_PER_PAGE);
        $paginator->setCurrentPage($page);

        return $paginator;
    }
}
