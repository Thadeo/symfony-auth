<?php

namespace App\Entity;

use App\Repository\AuthTypeProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuthTypeProviderRepository::class)]
class AuthTypeProvider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'provider')]
    private ?AuthType $type = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $orderling = null;

    #[ORM\Column]
    private ?bool $isPrimary = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedDate = null;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: AuthTypeProviderSettings::class)]
    private Collection $settings;

    public function __construct()
    {
        $this->settings = new ArrayCollection();
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

    public function getType(): ?AuthType
    {
        return $this->type;
    }

    public function setType(?AuthType $type): self
    {
        $this->type = $type;

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

    public function getOrderling(): ?string
    {
        return $this->orderling;
    }

    public function setOrderling(?string $orderling): self
    {
        $this->orderling = $orderling;

        return $this;
    }

    public function isIsPrimary(): ?bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(bool $isPrimary): self
    {
        $this->isPrimary = $isPrimary;

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

    public function setUpdatedDate(\DateTimeInterface $updatedDate): self
    {
        $this->updatedDate = $updatedDate;

        return $this;
    }

    /**
     * @return Collection<int, AuthTypeProviderSettings>
     */
    public function getSettings(): Collection
    {
        return $this->settings;
    }

    public function addSetting(AuthTypeProviderSettings $setting): self
    {
        if (!$this->settings->contains($setting)) {
            $this->settings->add($setting);
            $setting->setProvider($this);
        }

        return $this;
    }

    public function removeSetting(AuthTypeProviderSettings $setting): self
    {
        if ($this->settings->removeElement($setting)) {
            // set the owning side to null (unless already changed)
            if ($setting->getProvider() === $this) {
                $setting->setProvider(null);
            }
        }

        return $this;
    }
}
