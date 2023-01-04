<?php

namespace App\Entity;

use App\Repository\HealthCenterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HealthCenterRepository::class)]
class HealthCenter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column]
    private ?int $phonenumber = null;

    #[ORM\OneToMany(mappedBy: 'healthCenter', targetEntity: Orders::class)]
    private Collection $orders;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'healthCenters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Week $shipmentDay = null;

    #[ORM\OneToMany(mappedBy: 'healthCenter', targetEntity: MedicalSamples::class)]
    private Collection $medicalSamples;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->medicalSamples = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPhonenumber(): ?int
    {
        return $this->phonenumber;
    }

    public function setPhonenumber(int $phonenumber): self
    {
        $this->phonenumber = $phonenumber;

        return $this;
    }

    /**
     * @return Collection<int, Orders>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Orders $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setHealthCenter($this);
        }

        return $this;
    }

    public function removeOrder(Orders $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getHealthCenter() === $this) {
                $order->setHealthCenter(null);
            }
        }

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

    public function getShipmentDay(): ?Week
    {
        return $this->shipmentDay;
    }

    public function setShipmentDay(?Week $shipmentDay): self
    {
        $this->shipmentDay = $shipmentDay;

        return $this;
    }

    /**
     * @return Collection<int, MedicalSamples>
     */
    public function getMedicalSamples(): Collection
    {
        return $this->medicalSamples;
    }

    public function addMedicalSample(MedicalSamples $medicalSample): self
    {
        if (!$this->medicalSamples->contains($medicalSample)) {
            $this->medicalSamples->add($medicalSample);
            $medicalSample->setHealthCenter($this);
        }

        return $this;
    }

    public function removeMedicalSample(MedicalSamples $medicalSample): self
    {
        if ($this->medicalSamples->removeElement($medicalSample)) {
            // set the owning side to null (unless already changed)
            if ($medicalSample->getHealthCenter() === $this) {
                $medicalSample->setHealthCenter(null);
            }
        }

        return $this;
    }
}
