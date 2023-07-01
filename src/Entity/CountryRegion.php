<?php

namespace App\Entity;

use App\Repository\CountryRegionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryRegionRepository::class)]
class CountryRegion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\OneToMany(mappedBy: 'region', targetEntity: CountrySubRegion::class)]
    private Collection $subRegion;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedDate = null;

    #[ORM\OneToMany(mappedBy: 'region', targetEntity: Country::class)]
    private Collection $countries;

    public function __construct()
    {
        $this->subRegion = new ArrayCollection();
        $this->countries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, CountrySubRegion>
     */
    public function getSubRegion(): Collection
    {
        return $this->subRegion;
    }

    public function addSubRegion(CountrySubRegion $subRegion): static
    {
        if (!$this->subRegion->contains($subRegion)) {
            $this->subRegion->add($subRegion);
            $subRegion->setRegion($this);
        }

        return $this;
    }

    public function removeSubRegion(CountrySubRegion $subRegion): static
    {
        if ($this->subRegion->removeElement($subRegion)) {
            // set the owning side to null (unless already changed)
            if ($subRegion->getRegion() === $this) {
                $subRegion->setRegion(null);
            }
        }

        return $this;
    }

    public function getUpdatedDate(): ?\DateTimeInterface
    {
        return $this->updatedDate;
    }

    public function setUpdatedDate(\DateTimeInterface $updatedDate): static
    {
        $this->updatedDate = $updatedDate;

        return $this;
    }

    /**
     * @return Collection<int, Country>
     */
    public function getCountries(): Collection
    {
        return $this->countries;
    }

    public function addCountry(Country $country): static
    {
        if (!$this->countries->contains($country)) {
            $this->countries->add($country);
            $country->setRegion($this);
        }

        return $this;
    }

    public function removeCountry(Country $country): static
    {
        if ($this->countries->removeElement($country)) {
            // set the owning side to null (unless already changed)
            if ($country->getRegion() === $this) {
                $country->setRegion(null);
            }
        }

        return $this;
    }
}
