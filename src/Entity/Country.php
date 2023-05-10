<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
class Country
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

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $flag = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dialCode = null;

    #[ORM\ManyToOne]
    private ?Currency $currency = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedDate = null;

    #[ORM\OneToMany(mappedBy: 'Country', targetEntity: CountryState::class)]
    private Collection $countryStates;

    public function __construct()
    {
        $this->countryStates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getFlag(): ?string
    {
        return $this->flag;
    }

    public function setFlag(?string $flag): self
    {
        $this->flag = $flag;

        return $this;
    }

    public function getDialCode(): ?string
    {
        return $this->dialCode;
    }

    public function setDialCode(?string $dialCode): self
    {
        $this->dialCode = $dialCode;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getUpdatedDate(): ?\DateTimeInterface
    {
        return $this->updatedDate;
    }

    public function setUpdatedDate(?\DateTimeInterface $updatedDate): self
    {
        $this->updatedDate = $updatedDate;

        return $this;
    }

    /**
     * @return Collection<int, CountryState>
     */
    public function getCountryStates(): Collection
    {
        return $this->countryStates;
    }

    public function addCountryState(CountryState $countryState): self
    {
        if (!$this->countryStates->contains($countryState)) {
            $this->countryStates->add($countryState);
            $countryState->setCountry($this);
        }

        return $this;
    }

    public function removeCountryState(CountryState $countryState): self
    {
        if ($this->countryStates->removeElement($countryState)) {
            // set the owning side to null (unless already changed)
            if ($countryState->getCountry() === $this) {
                $countryState->setCountry(null);
            }
        }

        return $this;
    }
}
