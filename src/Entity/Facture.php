<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 14)]
    private ?string $numeroFacture = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'factures')]
    private ?User $client = null;

    #[ORM\Column]
    private ?float $montantHT = null;

    #[ORM\Column]
    private ?float $montantTVA = null;

    #[ORM\Column]
    private ?float $montantTTC = null;

    #[ORM\Column(length: 50)]
    private ?string $statutPaiement = null;

    #[ORM\Column(length: 50)]
    private ?string $modePaiement = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $paiementAt = null;

    /**
     * @var Collection<int, LigneFacture>
     */
    #[ORM\OneToMany(targetEntity: LigneFacture::class, mappedBy: 'facture', cascade: ['persist'])]
    private Collection $ligneFactures;

    #[ORM\Column(length: 10)]
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    private ?bool $seen = null;

    public function __construct()
    {
        $this->ligneFactures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroFacture(): ?string
    {
        return $this->numeroFacture;
    }

    public function setNumeroFacture(string $numeroFacture): static
    {
        $this->numeroFacture = $numeroFacture;

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

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getMontantHT(): ?float
    {
        return $this->montantHT;
    }

    public function setMontantHT(float $montantHT): static
    {
        $this->montantHT = $montantHT;

        return $this;
    }

    public function getMontantTVA(): ?float
    {
        return $this->montantTVA;
    }

    public function setMontantTVA(float $montantTVA): static
    {
        $this->montantTVA = $montantTVA;

        return $this;
    }

    public function getMontantTTC(): ?float
    {
        return $this->montantTTC;
    }

    public function setMontantTTC(float $montantTTC): static
    {
        $this->montantTTC = $montantTTC;

        return $this;
    }

    public function getStatutPaiement(): ?string
    {
        return $this->statutPaiement;
    }

    public function setStatutPaiement(string $statutPaiement): static
    {
        $this->statutPaiement = $statutPaiement;

        return $this;
    }

    public function getModePaiement(): ?string
    {
        return $this->modePaiement;
    }

    public function setModePaiement(string $modePaiement): static
    {
        $this->modePaiement = $modePaiement;

        return $this;
    }

    public function getPaiementAt(): ?\DateTimeImmutable
    {
        return $this->paiementAt;
    }

    public function setPaiementAt(\DateTimeImmutable $paiementAt): static
    {
        $this->paiementAt = $paiementAt;

        return $this;
    }

    /**
     * @return Collection<int, LigneFacture>
     */
    public function getLigneFactures(): Collection
    {
        return $this->ligneFactures;
    }

    public function addLigneFacture(LigneFacture $ligneFacture): static
    {
        if (!$this->ligneFactures->contains($ligneFacture)) {
            $this->ligneFactures->add($ligneFacture);
            $ligneFacture->setFacture($this);
        }

        return $this;
    }

    public function removeLigneFacture(LigneFacture $ligneFacture): static
    {
        if ($this->ligneFactures->removeElement($ligneFacture)) {
            // set the owning side to null (unless already changed)
            if ($ligneFacture->getFacture() === $this) {
                $ligneFacture->setFacture(null);
            }
        }

        return $this;
    }
    #[ORM\PrePersist]
    public function onPrePersist()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isSeen(): ?bool
    {
        return $this->seen;
    }

    public function setSeen(?bool $seen): static
    {
        $this->seen = $seen;

        return $this;
    }
    
}
