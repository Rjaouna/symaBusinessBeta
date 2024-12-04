<?php

namespace App\Entity;

use App\Repository\UsageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsageRepository::class)]
#[ORM\Table(name: '`usage`')]
class Usage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\ManyToOne]
    private ?SimType $type = null;

    #[ORM\Column]
    private ?int $consomation = null;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getType(): ?SimType
    {
        return $this->type;
    }

    public function setType(?SimType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getConsomation(): ?int
    {
        return $this->consomation;
    }

    public function setConsomation(int $consomation): static
    {
        $this->consomation = $consomation;

        return $this;
    }
}
