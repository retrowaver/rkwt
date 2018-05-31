<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ItemRepository")
 */
class Item
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="bigint")
     */
    private $itemId;

    /**
     * @ORM\Column(type="integer")
     */
    private $searchId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $initial;

    public function getId()
    {
        return $this->id;
    }

    public function getItemId(): ?int
    {
        return $this->itemId;
    }

    public function setItemId(int $itemId): self
    {
        $this->itemId = $itemId;

        return $this;
    }

    public function getSearchId(): ?int
    {
        return $this->searchId;
    }

    public function setSearchId(int $searchId): self
    {
        $this->searchId = $searchId;

        return $this;
    }

    public function getInitial(): ?bool
    {
        return $this->initial;
    }

    public function setInitial(bool $initial): self
    {
        $this->initial = $initial;

        return $this;
    }
}
