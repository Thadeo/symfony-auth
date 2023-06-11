<?php

namespace App\Entity;

use App\Repository\UserSettingsTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSettingsTypeRepository::class)]
class UserSettingsType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: UserSettings::class)]
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, UserSettings>
     */
    public function getSettings(): Collection
    {
        return $this->settings;
    }

    public function addSetting(UserSettings $setting): self
    {
        if (!$this->settings->contains($setting)) {
            $this->settings->add($setting);
            $setting->setType($this);
        }

        return $this;
    }

    public function removeSetting(UserSettings $setting): self
    {
        if ($this->settings->removeElement($setting)) {
            // set the owning side to null (unless already changed)
            if ($setting->getType() === $this) {
                $setting->setType(null);
            }
        }

        return $this;
    }
}
