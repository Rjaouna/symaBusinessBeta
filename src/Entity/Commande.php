<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;



#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    #[Groups('user_info')]
    private ?string $numero = null;

    #[ORM\Column]
    private ?int $qte = null;

    #[ORM\Column(nullable: true)]
    #[Groups('user_info')]
    private ?int $qtevalidee = null;

    #[ORM\Column(length: 50)]
    #[Groups('user_info')]
    private ?string $total = null;

    #[ORM\Column(length: 50)]
    #[Groups('user_info')]
    private ?string $status = null;

    #[ORM\Column]
    #[Groups('user_info')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups('user_info')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 50)]
    #[Groups('user_info')]
    private ?string $SimType = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?User $user = null;

    #[ORM\Column(length: 50)]
    #[Groups('user_info')]
    private ?string $code_client = null;

    #[ORM\Column(nullable: true)]
    private ?bool $modified = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getQte(): ?int
    {
        return $this->qte;
    }

    public function setQte(int $qte): static
    {
        $this->qte = $qte;

        return $this;
    }

    public function getQtevalidee(): ?int
    {
        return $this->qtevalidee;
    }

    public function setQtevalidee(?int $qtevalidee): static
    {
        $this->qtevalidee = $qtevalidee;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
   

    public function getSimType(): ?string
    {
        return $this->SimType;
    }

    public function setSimType(string $SimType): static
    {
        $this->SimType = $SimType;

        return $this;
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

    #[ORM\PrePersist]
    public function onPrePersist()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }
    #[ORM\PreUpdate]
    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getCodeClient(): ?string
    {
        return $this->code_client;
    }

    public function setCodeClient(string $code_client): static
    {
        $this->code_client = $code_client;

        return $this;
    }

    public function isModified(): ?bool
    {
        return $this->modified;
    }

    public function setModified(?bool $modified): static
    {
        $this->modified = $modified;

        return $this;
    }
}
