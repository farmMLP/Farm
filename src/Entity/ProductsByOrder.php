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

    #[ORM\Column]
    private ?int $quantitySent = null;

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
}
