<?php

namespace App\Entity;

use App\Repository\CarteSimRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: ['serialNumber'], message: "Ce numéro de série doit être unique.")]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: CarteSimRepository::class)]
class CarteSim
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 19)]
    #[Groups('user_info')]
    #[Assert\Regex(
        pattern: '/^\d+$/', // Vérifie que le champ contient uniquement des chiffres
        message: 'Le serial number ne doit contenir que des chiffres.'
    )]
    #[Assert\Length(
        max: 19,
        maxMessage: 'Le numéro de série ne doit pas dépasser 19 caractères.'
    )]
    private ?string $serialNumber = null;

    #[ORM\Column]
    private ?bool $reserved = null;

    #[ORM\ManyToOne(inversedBy: 'carteSims')]
    private ?User $purchasedBy = null;

    #[ORM\ManyToOne(inversedBy: 'carteSims')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SimType $type = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'carteSims')]
    private ?User $user = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $etat = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $usageFinale = null;

    #[ORM\ManyToOne(inversedBy: 'cartesSims')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Chapelet $chapelet = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $canalVente = null;

    /**
     * @var Collection<int, LigneFacture>
     */
    #[ORM\OneToMany(targetEntity: LigneFacture::class, mappedBy: 'produit')]
    private Collection $ligneFactures;

    public function __construct()
    {
        $this->ligneFactures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function isReserved(): ?bool
    {
        return $this->reserved;
    }

    public function setReserved(bool $reserved): static
    {
        $this->reserved = $reserved;

        return $this;
    }

    public function getPurchasedBy(): ?User
    {
        return $this->purchasedBy;
    }

    public function setPurchasedBy(?User $purchasedBy): static
    {
        $this->purchasedBy = $purchasedBy;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getUsageFinale(): ?string
    {
        return $this->usageFinale;
    }

    public function setUsageFinale(?string $usageFinale): static
    {
        $this->usageFinale = $usageFinale;

        return $this;
    }

    public function getChapelet(): ?Chapelet
    {
        return $this->chapelet;
    }

    public function setChapelet(?Chapelet $chapelet): static
    {
        $this->chapelet = $chapelet;

        return $this;
    }

    public function getCanalVente(): ?string
    {
        return $this->canalVente;
    }

    public function setCanalVente(?string $canalVente): static
    {
        $this->canalVente = $canalVente;

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
            $ligneFacture->setProduit($this);
        }

        return $this;
    }

    public function removeLigneFacture(LigneFacture $ligneFacture): static
    {
        if ($this->ligneFactures->removeElement($ligneFacture)) {
            // set the owning side to null (unless already changed)
            if ($ligneFacture->getProduit() === $this) {
                $ligneFacture->setProduit(null);
            }
        }

        return $this;
    }
}
