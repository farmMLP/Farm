<?php

namespace App\Entity;

use App\Repository\WeekRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WeekRepository::class)]
class Week
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $day = null;

    #[ORM\OneToMany(mappedBy: 'shipmentDay', targetEntity: HealthCenter::class)]
    private Collection $healthCenters;

    public function __construct()
    {
        $this->healthCenters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): self
    {
        $this->day = $day;

        return $this;
    }

    /**
     * @return Collection<int, HealthCenter>
     */
    public function getHealthCenters(): Collection
    {
        return $this->healthCenters;
    }

    public function addHealthCenter(HealthCenter $healthCenter): self
    {
        if (!$this->healthCenters->contains($healthCenter)) {
            $this->healthCenters->add($healthCenter);
            $healthCenter->setShipmentDay($this);
        }

        return $this;
    }

    public function removeHealthCenter(HealthCenter $healthCenter): self
    {
        if ($this->healthCenters->removeElement($healthCenter)) {
            // set the owning side to null (unless already changed)
            if ($healthCenter->getShipmentDay() === $this) {
                $healthCenter->setShipmentDay(null);
            }
        }

        return $this;
    }
}
