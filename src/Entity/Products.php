<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductsRepository::class)]
class Products
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $stock = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Batch::class, orphanRemoval: true)]
    private Collection $batches;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: MedicalSamples::class, orphanRemoval: true)]
    private Collection $medicalSamples;

    public function __construct()
    {
        $this->batches = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
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

    public function addStock(int $stock): self
    {
        $this->stock = $this->stock+$stock;

        return $this;
    }

    public function subStock(int $stock): self
    {
        $this->stock = $this->stock-$stock;

        return $this;
    }

    /**
     * @return Collection<int, Batch>
     */
    public function getBatches(): Collection
    {
        return $this->batches;
    }

    public function addBatch(Batch $batch): self
    {
        if (!$this->batches->contains($batch)) {
            $this->batches->add($batch);
            $batch->setProduct($this);
        }

        return $this;
    }

    public function removeBatch(Batch $batch): self
    {
        if ($this->batches->removeElement($batch)) {
            // set the owning side to null (unless already changed)
            if ($batch->getProduct() === $this) {
                $batch->setProduct(null);
            }
        }

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
            $medicalSample->setProduct($this);
        }

        return $this;
    }

    public function removeMedicalSample(MedicalSamples $medicalSample): self
    {
        if ($this->medicalSamples->removeElement($medicalSample)) {
            // set the owning side to null (unless already changed)
            if ($medicalSample->getProduct() === $this) {
                $medicalSample->setProduct(null);
            }
        }

        return $this;
    }
    public function __toString(){
      return strval($this->getName());
    }
}
