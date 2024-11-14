<?php

namespace App\Entity;

use App\Repository\LigneFactureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneFactureRepository::class)]
class LigneFacture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column]
    private ?float $prixUnitaireHT = null;

    #[ORM\Column]
    private ?float $montantTotalHT = null;

    #[ORM\ManyToOne(inversedBy: 'ligneFactures')]
    private ?Facture $facture = null;

    #[ORM\Column(length: 20)]
    private ?string $typeCarteSim = null;

    public function getId(): ?int
    {
        return $this->id;
    }

  

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrixUnitaireHT(): ?float
    {
        return $this->prixUnitaireHT;
    }

    public function setPrixUnitaireHT(float $prixUnitaireHT): static
    {
        $this->prixUnitaireHT = $prixUnitaireHT;

        return $this;
    }

    public function getMontantTotalHT(): ?float
    {
        return $this->montantTotalHT;
    }

    public function setMontantTotalHT(float $montantTotalHT): static
    {
        $this->montantTotalHT = $montantTotalHT;

        return $this;
    }

    public function getFacture(): ?Facture
    {
        return $this->facture;
    }

    public function setFacture(?Facture $facture): static
    {
        $this->facture = $facture;

        return $this;
    }

    public function getTypeCarteSim(): ?string
    {
        return $this->typeCarteSim;
    }

    public function setTypeCarteSim(string $typeCarteSim): static
    {
        $this->typeCarteSim = $typeCarteSim;

        return $this;
    }
    
    
}
