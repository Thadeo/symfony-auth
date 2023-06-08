<?php

namespace App\Entity;

use App\Repository\AuthTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuthTypeRepository::class)]
class AuthType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'types')]
    private ?Auth $auth = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $verifyType = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $shortDesc = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $longDesc = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedDate = null;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: AuthTypeProvider::class)]
    private Collection $provider;

    public function __construct()
    {
        $this->provider = new ArrayCollection();
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

    public function getAuth(): ?Auth
    {
        return $this->auth;
    }

    public function setAuth(?Auth $auth): self
    {
        $this->auth = $auth;

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

    public function getVerifyType(): ?string
    {
        return $this->verifyType;
    }

    public function setVerifyType(string $verifyType): self
    {
        $this->verifyType = $verifyType;

        return $this;
    }

    public function getShortDesc(): ?string
    {
        return $this->shortDesc;
    }

    public function setShortDesc(?string $shortDesc): self
    {
        $this->shortDesc = $shortDesc;

        return $this;
    }

    public function getLongDesc(): ?string
    {
        return $this->longDesc;
    }

    public function setLongDesc(?string $longDesc): self
    {
        $this->longDesc = $longDesc;

        return $this;
    }

    public function getActive(): ?bool
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
     * @return Collection<int, AuthTypeProvider>
     */
    public function getProvider(): Collection
    {
        return $this->provider;
    }

    public function addProvider(AuthTypeProvider $provider): self
    {
        if (!$this->provider->contains($provider)) {
            $this->provider->add($provider);
            $provider->setType($this);
        }

        return $this;
    }

    public function removeProvider(AuthTypeProvider $provider): self
    {
        if ($this->provider->removeElement($provider)) {
            // set the owning side to null (unless already changed)
            if ($provider->getType() === $this) {
                $provider->setType(null);
            }
        }

        return $this;
    }
}
