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
     * @ORM\Column(type="integer")
     */
    private $searchId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $wasInitial;

    /**
     * @ORM\Column(type="bigint")
     */
    private $auctionId;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $auctionTitle;

    /**
     * @ORM\Column(type="float")
     */
    private $auctionPrice;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $auctionImage;

    public function __construct(
        int $auctionId,
        string $auctionTitle,
        float $auctionPrice,
        ?string $auctionImage = null
    ) {
        $this->setAuctionId($auctionId);
        $this->setAuctionTitle($auctionTitle);
        $this->setAuctionPrice($auctionPrice);
        $this->setAuctionImage($auctionImage);
    }

    public function getId()
    {
        return $this->id;
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

    public function getWasInitial(): ?bool
    {
        return $this->wasInitial;
    }

    public function setWasInitial(bool $wasInitial): self
    {
        $this->wasInitial = $wasInitial;

        return $this;
    }

    public function getAuctionId(): ?int
    {
        return $this->auctionId;
    }

    public function setAuctionId(int $auctionId): self
    {
        $this->auctionId = $auctionId;

        return $this;
    }

    public function getAuctionTitle(): ?string
    {
        return $this->auctionTitle;
    }

    public function setAuctionTitle(string $auctionTitle): self
    {
        $this->auctionTitle = $auctionTitle;

        return $this;
    }

    public function getAuctionPrice(): ?float
    {
        return $this->auctionPrice;
    }

    public function setAuctionPrice(float $auctionPrice): self
    {
        $this->auctionPrice = $auctionPrice;

        return $this;
    }

    public function getAuctionImage(): ?string
    {
        return $this->auctionImage;
    }

    public function setAuctionImage(?string $auctionImage): self
    {
        $this->auctionImage = $auctionImage;

        return $this;
    }
}
