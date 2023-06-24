<?php

namespace App\Entity;

use App\Repository\RolesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RolesRepository::class)]
class Roles
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

    #[ORM\Column(length: 255)]
    private ?string $notes = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $longDesc = null;

    #[ORM\OneToMany(mappedBy: 'role', targetEntity: RolesPermission::class)]
    private Collection $rolesPermissions;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedDate = null;

    #[ORM\ManyToOne(inversedBy: 'roles')]
    private ?UserAccountType $accountType = null;

    public function __construct()
    {
        $this->rolesPermissions = new ArrayCollection();
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

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(string $notes): self
    {
        $this->notes = $notes;

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

    /**
     * @return Collection<int, RolesPermission>
     */
    public function getRolesPermissions(): Collection
    {
        return $this->rolesPermissions;
    }

    public function addRolesPermission(RolesPermission $rolesPermission): self
    {
        if (!$this->rolesPermissions->contains($rolesPermission)) {
            $this->rolesPermissions->add($rolesPermission);
            $rolesPermission->setRole($this);
        }

        return $this;
    }

    public function removeRolesPermission(RolesPermission $rolesPermission): self
    {
        if ($this->rolesPermissions->removeElement($rolesPermission)) {
            // set the owning side to null (unless already changed)
            if ($rolesPermission->getRole() === $this) {
                $rolesPermission->setRole(null);
            }
        }

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

    public function getAccountType(): ?UserAccountType
    {
        return $this->accountType;
    }

    public function setAccountType(?UserAccountType $accountType): self
    {
        $this->accountType = $accountType;

        return $this;
    }
}
