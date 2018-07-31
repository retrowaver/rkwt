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

        // Prepare items and assign them to search
        $this->itemService->setStatus($items, 0);
        $this->itemService->setSearch($items, $search);
        $search->setItems($items);

        // Change search's status (no longer fresh search)
        $search->setStatus(1);

        // Update timestamps
        $search->setTimeLastSearched(new \DateTime('now'));
        $search->setTimeLastFullySearched(new \DateTime('now'));
    }

    private function updateStandardSearch(Search $search): void
    {
        // Quick search is much faster and usually sufficient, but full search
        // should be done from time to time due to following reasons:
        // - it removes old / irrelevant items from db
        // - it kind of synchronises local state with Allegro, which is useful
        // in some cases (i.e. temporary unavailability of local server or Allegro API)
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

        // Add new items to search
        $items = new ArrayCollection(
            $newItems
        );
        $search->addItems($items);

        // Update timestamp
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

        // Merge all items into one collection
        $items = new ArrayCollection(
            array_merge(
                $newItems,
                $activeItems,
                $inactiveItems
            )
        );

        // Set items of search
        $search->setItems($items);

        // Update timestamps
        $search->setTimeLastSearched(new \DateTime('now'));
        $search->setTimeLastFullySearched(new \DateTime('now'));
    }

    private function shouldItemBeKept(Item $item): bool
    {
        return (
            $item->getStatus() !== 0
            && $item->getTimeFound() > new \DateTime(self::OBSOLETE_ITEM_MAX_AGE . ' days ago')
        );
    }
}
