<?php

namespace App\Entity;

use App\Repository\SessionsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionsRepository::class)]
class Sessions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BINARY, unique: true, name: 'ids')]
    private $ids = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    private ?User $user = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $data = null;

    #[ORM\Column]
    private ?int $time = null;

    #[ORM\Column]
    private ?int $lifetime = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    private ?UserDevices $device = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIds()
    {
        return $this->ids;
    }

    public function setIds($ids): self
    {
        $this->ids = $ids;

        return $this;
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

    public function getData()
    {
        return $this->data;
    }

    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(int $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getLifetime(): ?int
    {
        return $this->lifetime;
    }

    public function setLifetime(int $lifetime): self
    {
        $this->lifetime = $lifetime;

        return $this;
    }

    public function getDevice(): ?UserDevices
    {
        return $this->device;
    }

    public function setDevice(?UserDevices $device): self
    {
        $this->device = $device;

        return $this;
    }
}
