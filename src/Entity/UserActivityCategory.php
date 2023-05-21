<?php

namespace App\Entity;

use App\Repository\UserActivityCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserActivityCategoryRepository::class)]
class UserActivityCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $longDesc = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: UserActivity::class)]
    private Collection $userActivities;

    public function __construct()
    {
        $this->userActivities = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

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
     * @return Collection<int, UserActivity>
     */
    public function getUserActivities(): Collection
    {
        return $this->userActivities;
    }

    public function addUserActivity(UserActivity $userActivity): self
    {
        if (!$this->userActivities->contains($userActivity)) {
            $this->userActivities->add($userActivity);
            $userActivity->setCategory($this);
        }

        return $this;
    }

    public function removeUserActivity(UserActivity $userActivity): self
    {
        if ($this->userActivities->removeElement($userActivity)) {
            // set the owning side to null (unless already changed)
            if ($userActivity->getCategory() === $this) {
                $userActivity->setCategory(null);
            }
        }

        return $this;
    }
}
