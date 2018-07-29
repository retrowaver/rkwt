<?php

namespace App\Service\Tools;

use App\Entity\Item;
use App\Entity\Search;
use App\Service\Allegro\AllegroService;
use App\Service\Allegro\AllegroServiceInterface;

use App\Service\ItemServiceInterface;

use App\Repository\SearchRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class SearchUpdateService implements SearchUpdateServiceInterface
{
    const OBSOLETE_ITEM_MAX_AGE = 7; // days

    private $em;
    private $allegro;
    private $itemService;
    private $searchRepository;

    public function __construct(
        EntityManagerInterface $em,
        AllegroServiceInterface $allegro,
        ItemServiceInterface $itemService,
        SearchRepository $searchRepository
    ) {
        $this->em = $em;
        $this->allegro = $allegro;
        $this->itemService = $itemService;
        $this->searchRepository = $searchRepository;
    }

	public function updateSearches(Collection $searches): void
    {
        $start = time();

        foreach ($searches as $search) {
            $this->updateSearch($search);
        }

        printf("%s - search update ended (took %d seconds, updated %d searches)\n", date('Y-m-d H:i:s'), time() - $start, count($searches));
    }

    public function updateSearch(Search $search): void
    {
        switch ($search->getStatus()) {
            case 0:
                $this->updateFreshSearch($search);
                break;
            case 1:
                $this->updateStandardSearch($search);
                break;
        }

        $this->em->flush();
    }

    public function getActiveSearches(): Collection
    {
        return $this->searchRepository->findByStatus(
            [0, 1]
        );
    }

    private function updateFreshSearch(Search $search): void
    {
        // Get live items from API
        $items = $this->allegro->getItems(
            $search->getFiltersForApi()
        );

        // Set items' status to .........................................................................
        $this->itemService->setStatus($items, 0);

        //
        $this->itemService->setSearch($items, $search);

        //
        $search->setItems($items);

        // Change search's status to "default active" (1)
        $search->setStatus(1);

        $search->setTimeLastSearched(new \DateTime('now'));
        $search->setTimeLastFullySearched(new \DateTime('now'));
    }

    private function updateStandardSearch(Search $search): void
    {
        if (
            $search->getTimeLastSearched() > new \DateTime('1 hour ago')
            && $search->getTimeLastFullySearched() > new \DateTime('1 day ago')
        ) {
            $this->updateStandardSearchQuickly($search);
        } else {
            $this->updateStandardSearchFully($search);
        }
    }

    private function updateStandardSearchQuickly(Search $search)
    {
        // Get live items from API
        $itemsFromAllegro = $this->allegro->getItems(
            $search->getFiltersForApi(),
            true
        );

        // Get items that haven't been seen before
        $newItems = array_udiff(
            $itemsFromAllegro->toArray(),
            $search->getItems()->toArray(),
            function ($a, $b) {return $a->getAuctionId() <=> $b->getAuctionId();}
        );
        $this->itemService->setStatus($newItems, 2);
        $this->itemService->setSearch($newItems, $search);

        $items = new ArrayCollection(
            $newItems
        );

        $search->addItems($items);

        $search->setTimeLastSearched(new \DateTime('now'));
    }

    private function updateStandardSearchFully(Search $search)
    {
        // Get live items from API
        $itemsFromAllegro = $this->allegro->getItems(
            $search->getFiltersForApi()
        );

        // Get items that haven't been seen before
        $newItems = array_udiff(
            $itemsFromAllegro->toArray(),
            $search->getItems()->toArray(),
            function ($a, $b) {return $a->getAuctionId() <=> $b->getAuctionId();}
        );
        $this->itemService->setStatus($newItems, 2);
        $this->itemService->setSearch($newItems, $search);

        // Get items present both live on Allegro and in database
        $activeItems = array_uintersect(
            $search->getItems()->toArray(),
            $itemsFromAllegro->toArray(),
            function ($a, $b) {return $a->getAuctionId() <=> $b->getAuctionId();}
        );

        // Get items that are present in database, but not live on Allegro (anymore).
        $inactiveItems = array_udiff(
            $search->getItems()->toArray(),
            $activeItems,
            function ($a, $b) {return $a->getAuctionId() <=> $b->getAuctionId();}
        );
        // Filter out items with status 0 (hidden by their users or coming from initial search)
        // or older than X days (they can't and don't have to be stored forever)
        $inactiveItems = array_filter($inactiveItems, [$this, 'shouldItemBeKept']);


        //dump($newItems);
        //dump($activeItems);
        //dump($inactiveItems);
        //exit;


        // Merge all items
        $items = new ArrayCollection(
            array_merge(
                $newItems,
                $activeItems,
                $inactiveItems
            )
        );

        /*foreach ($items as $item) {
            $item->setId(null);
        }*/

        //dump($items);
        //exit;

        //echo $items->count();


        $search->setItems($items);


        //echo $search->getItems()->count();
        //exit;

        $search->setTimeLastSearched(new \DateTime('now'));
        $search->setTimeLastFullySearched(new \DateTime('now'));

        //dodac do current searcha itemy, ktorych nie ma w nim (ze statusem 2) ?????????? co autor mial na mysli?        
    }

    private function shouldItemBeKept(Item $item): bool
    {
        return (
            $item->getStatus() !== 0
            && $item->getTimeFound() > new \DateTime(self::OBSOLETE_ITEM_MAX_AGE . ' days ago')
        );
    }
}
