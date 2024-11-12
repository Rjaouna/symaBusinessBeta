<?php

namespace App\Entity;

use App\Repository\LignesCommandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: LignesCommandeRepository::class)]
class LignesCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?CarteSim $cartesSims = null;

    #[ORM\ManyToOne(inversedBy: 'lignesCommandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\Column(length: 50)]
    private ?string $prixUnitaire = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'lignesCommandes')]
    private ?SimType $typeSim = null;

    #[ORM\Column(length: 19)]
    private ?string $serialNumber = null;

    #[ORM\Column(length: 50)]
    private ?string $numeroCommande = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCartesSims(): ?CarteSim
    {
        return $this->cartesSims;
    }

    public function setCartesSims(?CarteSim $cartesSims): static
    {
        $this->cartesSims = $cartesSims;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): static
    {
        $this->commande = $commande;

        return $this;
    }

    public function getPrixUnitaire(): ?string
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(string $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;

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

    public function getTypeSim(): ?SimType
    {
        return $this->typeSim;
    }

    public function setTypeSim(?SimType $typeSim): static
    {
        $this->typeSim = $typeSim;

        return $this;
    }
    #[ORM\PrePersist]
    public function onPrePersist()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(string $serialNumber): static
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    public function getNumeroCommande(): ?string
    {
        return $this->numeroCommande;
    }

    public function setNumeroCommande(string $numeroCommande): static
    {
        $this->numeroCommande = $numeroCommande;

        return $this;
    }
}
