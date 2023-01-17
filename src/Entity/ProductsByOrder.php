<?php

namespace App\Entity;

use App\Repository\ProductsByOrderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductsByOrderRepository::class)]
class ProductsByOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantityRequested = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantitySent = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Products $product = null;

    #[ORM\ManyToOne(inversedBy: 'productsByOrders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Orders $petition = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantityRequested(): ?int
    {
        return $this->quantityRequested;
    }

    public function setQuantityRequested(int $quantityRequested): self
    {
        $this->quantityRequested = $quantityRequested;

        return $this;
    }

    public function getQuantitySent(): ?int
    {
        return $this->quantitySent;
    }

    public function setQuantitySent(int $quantitySent): self
    {
        $this->quantitySent = $quantitySent;

        return $this;
    }

    public function getProduct(): ?Products
    {
        return $this->product;
    }

    public function setProduct(?Products $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getPetition(): ?Orders
    {
        return $this->petition;
    }

    public function setPetition(?Orders $petition): self
    {
        $this->petition = $petition;

        return $this;
    }
}
