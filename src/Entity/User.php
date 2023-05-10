<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Country $country = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $middleName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserPhone::class)]
    private Collection $userPhones;

    #[ORM\ManyToOne]
    private ?UserPhone $phone = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserAddress::class)]
    private Collection $userAddresses;

    #[ORM\ManyToOne]
    private ?UserAddress $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $account = null;

    #[ORM\ManyToOne]
    private ?UserAccountType $accountType = null;

    #[ORM\Column]
    private ?bool $isVerified = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?UserRoles $role = null;

    public function __construct()
    {
        $this->userPhones = new ArrayCollection();
        $this->userAddresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): self
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @return Collection<int, UserPhone>
     */
    public function getUserPhones(): Collection
    {
        return $this->userPhones;
    }

    public function addUserPhone(UserPhone $userPhone): self
    {
        if (!$this->userPhones->contains($userPhone)) {
            $this->userPhones->add($userPhone);
            $userPhone->setUser($this);
        }

        return $this;
    }

    public function removeUserPhone(UserPhone $userPhone): self
    {
        if ($this->userPhones->removeElement($userPhone)) {
            // set the owning side to null (unless already changed)
            if ($userPhone->getUser() === $this) {
                $userPhone->setUser(null);
            }
        }

        return $this;
    }

    public function getPhone(): ?UserPhone
    {
        return $this->phone;
    }

    public function setPhone(?UserPhone $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, UserAddress>
     */
    public function getUserAddresses(): Collection
    {
        return $this->userAddresses;
    }

    public function addUserAddress(UserAddress $userAddress): self
    {
        if (!$this->userAddresses->contains($userAddress)) {
            $this->userAddresses->add($userAddress);
            $userAddress->setUser($this);
        }

        return $this;
    }

    public function removeUserAddress(UserAddress $userAddress): self
    {
        if ($this->userAddresses->removeElement($userAddress)) {
            // set the owning side to null (unless already changed)
            if ($userAddress->getUser() === $this) {
                $userAddress->setUser(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?UserAddress
    {
        return $this->address;
    }

    public function setAddress(?UserAddress $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAccount(): ?string
    {
        return $this->account;
    }

    public function setAccount(?string $account): self
    {
        $this->account = $account;

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

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getRole(): ?UserRoles
    {
        return $this->role;
    }

    public function setRole(?UserRoles $role): self
    {
        // unset the owning side of the relation if necessary
        if ($role === null && $this->role !== null) {
            $this->role->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($role !== null && $role->getUser() !== $this) {
            $role->setUser($this);
        }

        $this->role = $role;

        return $this;
    }
}
