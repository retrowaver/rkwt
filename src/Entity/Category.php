<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $catId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $catName;

    /**
     * @ORM\Column(type="integer")
     */
    private $catParent;

    /**
     * @ORM\Column(type="integer")
     */
    private $catPosition;

    /**
     * @ORM\Column(type="boolean")
     */
    private $catIsLeaf;

    public function getId()
    {
        return $this->id;
    }

    public function getCatId(): ?int
    {
        return $this->catId;
    }

    public function setCatId(int $catId): self
    {
        $this->catId = $catId;

        return $this;
    }

    public function getCatName(): ?string
    {
        return $this->catName;
    }

    public function setCatName(string $catName): self
    {
        $this->catName = $catName;

        return $this;
    }

    public function getCatParent(): ?int
    {
        return $this->catParent;
    }

    public function setCatParent(int $catParent): self
    {
        $this->catParent = $catParent;

        return $this;
    }

    public function getCatPosition(): ?int
    {
        return $this->catPosition;
    }

    public function setCatPosition(int $catPosition): self
    {
        $this->catPosition = $catPosition;

        return $this;
    }

    public function getCatIsLeaf(): ?bool
    {
        return $this->catIsLeaf;
    }

    public function setCatIsLeaf(bool $catIsLeaf): self
    {
        $this->catIsLeaf = $catIsLeaf;

        return $this;
    }
}
