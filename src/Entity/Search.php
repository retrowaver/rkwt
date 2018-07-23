<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SearchRepository")
 */
class Search
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"search_edit"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="searches")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"search_edit", "search_save"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Filter", mappedBy="search", orphanRemoval=true, cascade={"persist", "remove"})
     * @Groups({"search_save"})
     */
    private $filters;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Item", mappedBy="search", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $items;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $timeLastSearched;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $timeLastFullySearched;

    public function __construct()
    {
        $this->filters = new ArrayCollection();
        $this->items = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @Groups({"default"})
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setFilters(Collection $filters): self
    {
        $this->filters->clear();
        foreach($filters as $filter) {
            $this->addFilter($filter);
        }

        return $this;
    }




    /*public function clearFilters()
    {
        $this->filters->clear();
    }*/






    /**
     * @return Collection|Filter[]
     */
    public function getFilters(): Collection
    {
        return $this->filters;
    }

    public function addFilter(Filter $filter): self
    {
        if (!$this->filters->contains($filter)) {
            $this->filters[] = $filter;
            $filter->setSearch($this);
        }

        return $this;
    }

    public function removeFilter(Filter $filter): self
    {
        if ($this->filters->contains($filter)) {
            $this->filters->removeElement($filter);
            // set the owning side to null (unless already changed)
            if ($filter->getSearch() === $this) {
                $filter->setSearch(null);
            }
        }

        return $this;
    }

    public function getFiltersIds(): array
    {
        return $this->getFilters()->map(
            function($s) {
                return $s->getFilterId();
            }
        )->toArray();
    }

    /**
     * @Groups({"search_edit"})
     */
    public function getFiltersForApi(): array
    {
        $filters = [];

        foreach ($this->filters as $current) {
            $filter = ['filterId' => $current->getFilterId()];

            $values = $current->getFilterValues();
            if (!$values->isEmpty()) {
                $filter['filterValueId'] = [];

                foreach ($values as $value) {
                    $filter['filterValueId'][] = $value->getFilterValue();
                }
            } else {
                $filter['filterValueRange'] = [];

                if ($current->getValueRangeMin() !== null) {
                    $filter['filterValueRange']['rangeValueMin'] = $current->getValueRangeMin();
                }

                if ($current->getValueRangeMax() !== null) {
                    $filter['filterValueRange']['rangeValueMax'] = $current->getValueRangeMax();
                }
            }

            $filters[] = $filter;
        }

        return $filters;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Item[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setSearch($this);
        }

        return $this;
    }

    public function addItems(Collection $items): self
    {
        foreach ($items as $item) {
            $this->addItem($item);
        }

        return $this;
    }

    public function setItems(Collection $items): self
    {
        $this->items->clear();
        $this->addItems($items);

        return $this;
    }

    public function getTimeLastSearched(): ?\DateTimeInterface
    {
        return $this->timeLastSearched;
    }

    public function setTimeLastSearched(?\DateTimeInterface $timeLastSearched): self
    {
        $this->timeLastSearched = $timeLastSearched;

        return $this;
    }

    public function getTimeLastFullySearched(): ?\DateTimeInterface
    {
        return $this->timeLastFullySearched;
    }

    public function setTimeLastFullySearched(?\DateTimeInterface $timeLastFullySearched): self
    {
        $this->timeLastFullySearched = $timeLastFullySearched;

        return $this;
    }

    /*public function removeItem(Item $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getSearch() === $this) {
                $item->setSearch(null);
            }
        }

        return $this;
    }*/
}
