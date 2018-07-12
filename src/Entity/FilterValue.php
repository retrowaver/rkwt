<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FilterValueRepository")
 */
class FilterValue
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"search_save"})
     */
    private $filterValue;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Filter", inversedBy="filterValues")
     * @ORM\JoinColumn(nullable=false)
     */
    private $filter;

    public function getId()
    {
        return $this->id;
    }

    public function getFilterValue(): ?string
    {
        return $this->filterValue;
    }

    public function setFilterValue(string $filterValue): self
    {
        $this->filterValue = $filterValue;

        return $this;
    }

    public function getFilter(): ?Filter
    {
        return $this->filter;
    }

    public function setFilter(?Filter $filter): self
    {
        $this->filter = $filter;

        return $this;
    }
}
