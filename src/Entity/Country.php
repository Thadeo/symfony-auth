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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $iso = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $isoNumeric = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $isoNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $capital = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $flag = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dialCode = null;

    #[ORM\ManyToOne]
    private ?Currency $currency = null;

    #[ORM\OneToMany(mappedBy: 'Country', targetEntity: CountryState::class)]
    private Collection $countryStates;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tld = null;

    #[ORM\ManyToOne(inversedBy: 'countries')]
    private ?CountryRegion $region = null;

    #[ORM\ManyToOne(inversedBy: 'countries')]
    private ?CountrySubRegion $subRegion = null;

    #[ORM\Column(nullable: true)]
    private array $timezones = [];

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 8, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 8, nullable: true)]
    private ?string $longitude = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedDate = null;

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

    public function getIso(): ?string
    {
        return $this->iso;
    }

    public function setIso(?string $iso): static
    {
        $this->iso = $iso;

        return $this;
    }

    public function getIsoNumeric(): ?string
    {
        return $this->isoNumeric;
    }

    public function setIsoNumeric(?string $isoNumeric): static
    {
        $this->isoNumeric = $isoNumeric;

        return $this;
    }

    public function getIsoNumber(): ?string
    {
        return $this->isoNumber;
    }

    public function setIsoNumber(?string $isoNumber): static
    {
        $this->isoNumber = $isoNumber;

        return $this;
    }

    public function getCapital(): ?string
    {
        return $this->capital;
    }

    public function setCapital(?string $capital): static
    {
        $this->capital = $capital;

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

    public function getTld(): ?string
    {
        return $this->tld;
    }

    public function setTid(?string $tld): static
    {
        $this->tld = $tld;

        return $this;
    }

    public function getRegion(): ?CountryRegion
    {
        return $this->region;
    }

    public function setRegion(?CountryRegion $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getSubRegion(): ?CountrySubRegion
    {
        return $this->subRegion;
    }

    public function setSubRegion(?CountrySubRegion $subRegion): static
    {
        $this->subRegion = $subRegion;

        return $this;
    }

    public function getTimezones(): array
    {
        return $this->timezones;
    }

    public function setTimezones(?array $timezones): static
    {
        $this->timezones = $timezones;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;

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
}
