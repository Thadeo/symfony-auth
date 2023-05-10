<?php

namespace App\Entity;

use App\Repository\UserCustomRolesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserCustomRolesRepository::class)]
class UserCustomRoles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $longDesc = null;

    #[ORM\OneToMany(mappedBy: 'role', targetEntity: UserCustomRolesPermission::class)]
    private Collection $userCustomRolesPermissions;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedDate = null;

    public function __construct()
    {
        $this->userCustomRolesPermissions = new ArrayCollection();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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

    public function setNotes(?string $notes): self
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
     * @return Collection<int, UserCustomRolesPermission>
     */
    public function getUserCustomRolesPermissions(): Collection
    {
        return $this->userCustomRolesPermissions;
    }

    public function addUserCustomRolesPermission(UserCustomRolesPermission $userCustomRolesPermission): self
    {
        if (!$this->userCustomRolesPermissions->contains($userCustomRolesPermission)) {
            $this->userCustomRolesPermissions->add($userCustomRolesPermission);
            $userCustomRolesPermission->setRole($this);
        }

        return $this;
    }

    public function removeUserCustomRolesPermission(UserCustomRolesPermission $userCustomRolesPermission): self
    {
        if ($this->userCustomRolesPermissions->removeElement($userCustomRolesPermission)) {
            // set the owning side to null (unless already changed)
            if ($userCustomRolesPermission->getRole() === $this) {
                $userCustomRolesPermission->setRole(null);
            }
        }

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
}
