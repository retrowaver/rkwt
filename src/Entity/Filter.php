<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FilterRepository")
 */
class Filter
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Search", inversedBy="filters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $search;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FilterValue", mappedBy="filter", orphanRemoval=true, cascade={"persist", "remove"})
     * @Groups({"search_save"})
     */
    private $filterValues;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"search_save"})
     */
    private $valueRangeMin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"search_save"})
     */
    private $valueRangeMax;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"search_save"})
     */
    private $filterId;

    public function __construct()
    {
        $this->filterValues = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSearch(): ?Search
    {
        return $this->search;
    }

    public function setSearch(?Search $search): self
    {
        $this->search = $search;

        return $this;
    }

    public function getValueRangeMin(): ?string
    {
        return $this->valueRangeMin;
    }

    public function setValueRangeMin(?string $valueRangeMin): self
    {
        $this->valueRangeMin = $valueRangeMin;

        return $this;
    }

    public function getValueRangeMax(): ?string
    {
        return $this->valueRangeMax;
    }

    public function setValueRangeMax(?string $valueRangeMax): self
    {
        $this->valueRangeMax = $valueRangeMax;

        return $this;
    }

    /**
     * @return Collection|FilterValue[]
     */
    public function getFilterValues(): Collection
    {
        return $this->filterValues;
    }

    public function addFilterValue(FilterValue $filterValue): self
    {
        if (!$this->filterValues->contains($filterValue)) {
            $this->filterValues[] = $filterValue;
            $filterValue->setFilter($this);
        }

        return $this;
    }

    public function removeFilterValue(FilterValue $filterValue): self
    {
        if ($this->filterValues->contains($filterValue)) {
            $this->filterValues->removeElement($filterValue);
            // set the owning side to null (unless already changed)
            if ($filterValue->getFilter() === $this) {
                $filterValue->setFilter(null);
            }
        }

        return $this;
    }

    public function getFilterId(): ?string
    {
        return $this->filterId;
    }

    public function setFilterId(string $filterId): self
    {
        $this->filterId = $filterId;

        return $this;
    }
}
