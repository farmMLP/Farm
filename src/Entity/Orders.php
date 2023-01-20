<?php

namespace App\Entity;

use App\Repository\OrdersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdersRepository::class)]
class Orders
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $memo = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?HealthCenter $healthCenter = null;

    #[ORM\OneToMany(mappedBy: 'petition', targetEntity: ProductsByOrder::class, orphanRemoval: true)]
    private Collection $productsByOrders;

    public function __construct()
    {
        $this->productsByOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getMemo(): ?string
    {
        return $this->memo;
    }

    public function setMemo(string $memo): self
    {
        $this->memo = $memo;

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

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function getStatusId(): int
    {
        return $this->status->id;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

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

    /**
     * @return Collection<int, ProductsByOrder>
     */
    public function getProductsByOrders(): Collection
    {
        return $this->productsByOrders;
    }

    public function addProductsByOrder(ProductsByOrder $productsByOrder): self
    {
        if (!$this->productsByOrders->contains($productsByOrder)) {
            $this->productsByOrders->add($productsByOrder);
            $productsByOrder->setPetition($this);
        }

        return $this;
    }

    public function removeProductsByOrder(ProductsByOrder $productsByOrder): self
    {
        if ($this->productsByOrders->removeElement($productsByOrder)) {
            // set the owning side to null (unless already changed)
            if ($productsByOrder->getPetition() === $this) {
                $productsByOrder->setPetition(null);
            }
        }

        return $this;
    }

}
