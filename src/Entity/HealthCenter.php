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
    private ?string $phonenumber = null;

    #[ORM\OneToMany(mappedBy: 'healthCenter', targetEntity: Orders::class, orphanRemoval: true)]
    private Collection $orders;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'healthCenters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Week $shipmentDay = null;

    #[ORM\OneToMany(mappedBy: 'healthCenter', targetEntity: MedicalSamples::class, orphanRemoval: true)]
    private Collection $medicalSamples;

    #[ORM\OneToMany(mappedBy: 'healthCenter', targetEntity: User::class, orphanRemoval: true)]
    private Collection $users;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->medicalSamples = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    public function getPhonenumber(): ?string
    {
        return $this->phonenumber;
    }

    public function setPhonenumber(string $phonenumber): self
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

    public function __toString(){
      return strval($this->getName());
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setHealthCenter($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getHealthCenter() === $this) {
                $user->setHealthCenter(null);
            }
        }

        return $this;
    }
}
