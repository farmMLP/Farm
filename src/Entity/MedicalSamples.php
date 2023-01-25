<?php

namespace App\Entity;

use App\Repository\MedicalSamplesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MedicalSamplesRepository::class)]
class MedicalSamples
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $stock = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $expirationDate = null;

    #[ORM\ManyToOne(inversedBy: 'medicalSamples')]
    #[ORM\JoinColumn(nullable: false)]
    private ?HealthCenter $healthCenter = null;

    #[ORM\ManyToOne(inversedBy: 'medicalSamples')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Products $product = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeImmutable
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(\DateTimeImmutable $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function getHealthCenter(): ?HealthCenter
    {
        return $this->healthCenter;
    }

    public function setHealthCenter(?HealthCenter $healthCenter): self
    {
        $this->healthCenter = $healthCenter;

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
    
    public function __toString(){
      return $this->getStock();
    }
}
