<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ItemRepository")
 */
class Item
{
    const MAX_PER_PAGE = 5;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

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

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Search", inversedBy="items", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $search;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $timeFound;

    public function __construct(
        int $auctionId,
        string $auctionTitle,
        float $auctionPrice,
        ?string $auctionImage = null,
        ?int $status = 0,
        ?\DateTimeInterface $timeFound = null
    ) {
        $this->setAuctionId($auctionId);
        $this->setAuctionTitle($auctionTitle);
        $this->setAuctionPrice($auctionPrice);
        $this->setAuctionImage($auctionImage);
        $this->setStatus($status);
        $this->setTimeFound($timeFound ?? new \DateTime("now"));
    }

    public function getId()
    {
        return $this->id;
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

    public function getSearch(): ?Search
    {
        return $this->search;
    }

    public function setSearch(?Search $search): self
    {
        $this->search = $search;

        return $this;
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

    public function getTimeFound(): ?\DateTimeInterface
    {
        return $this->timeFound;
    }

    public function setTimeFound(\DateTimeInterface $timeFound): self
    {
        $this->timeFound = $timeFound;

        return $this;
    }
}
