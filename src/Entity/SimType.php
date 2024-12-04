<?php

namespace App\Entity;

use App\Repository\SimTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: SimTypeRepository::class)]
class SimType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $code = null;

    #[ORM\Column(length: 50)]
    private ?string $prix = null;

    /**
     * @var Collection<int, CarteSim>
     */
    #[ORM\OneToMany(targetEntity: CarteSim::class, mappedBy: 'type')]
    private Collection $carteSims;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, LignesCommande>
     */
    #[ORM\OneToMany(targetEntity: LignesCommande::class, mappedBy: 'typeSim')]
    private Collection $lignesCommandes;

    /**
     * @var Collection<int, PendingSimCards>
     */
    #[ORM\OneToMany(targetEntity: PendingSimCards::class, mappedBy: 'type')]
    private Collection $pendingSimCards;

    /**
     * @var Collection<int, Chapelet>
     */
    #[ORM\OneToMany(targetEntity: Chapelet::class, mappedBy: 'typeCartes')]
    private Collection $chapelets;

    /**
     * @var Collection<int, Commande>
     */
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'TypeSim')]
    private Collection $commandes;

    public function __construct()
    {
        $this->carteSims = new ArrayCollection();
        $this->lignesCommandes = new ArrayCollection();
        $this->pendingSimCards = new ArrayCollection();
        $this->chapelets = new ArrayCollection();
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection<int, CarteSim>
     */
    public function getCarteSims(): Collection
    {
        return $this->carteSims;
    }

    public function addCarteSim(CarteSim $carteSim): static
    {
        if (!$this->carteSims->contains($carteSim)) {
            $this->carteSims->add($carteSim);
            $carteSim->setType($this);
        }

        return $this;
    }

    public function removeCarteSim(CarteSim $carteSim): static
    {
        if ($this->carteSims->removeElement($carteSim)) {
            // set the owning side to null (unless already changed)
            if ($carteSim->getType() === $this) {
                $carteSim->setType(null);
            }
        }

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

    /**
     * @return Collection<int, LignesCommande>
     */
    public function getLignesCommandes(): Collection
    {
        return $this->lignesCommandes;
    }

    public function addLignesCommande(LignesCommande $lignesCommande): static
    {
        if (!$this->lignesCommandes->contains($lignesCommande)) {
            $this->lignesCommandes->add($lignesCommande);
            $lignesCommande->setTypeSim($this);
        }

        return $this;
    }

    public function removeLignesCommande(LignesCommande $lignesCommande): static
    {
        if ($this->lignesCommandes->removeElement($lignesCommande)) {
            // set the owning side to null (unless already changed)
            if ($lignesCommande->getTypeSim() === $this) {
                $lignesCommande->setTypeSim(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nom;
    }

    /**
     * @return Collection<int, PendingSimCards>
     */
    public function getPendingSimCards(): Collection
    {
        return $this->pendingSimCards;
    }

    public function addPendingSimCard(PendingSimCards $pendingSimCard): static
    {
        if (!$this->pendingSimCards->contains($pendingSimCard)) {
            $this->pendingSimCards->add($pendingSimCard);
            $pendingSimCard->setType($this);
        }

        return $this;
    }

    public function removePendingSimCard(PendingSimCards $pendingSimCard): static
    {
        if ($this->pendingSimCards->removeElement($pendingSimCard)) {
            // set the owning side to null (unless already changed)
            if ($pendingSimCard->getType() === $this) {
                $pendingSimCard->setType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Chapelet>
     */
    public function getChapelets(): Collection
    {
        return $this->chapelets;
    }

    public function addChapelet(Chapelet $chapelet): static
    {
        if (!$this->chapelets->contains($chapelet)) {
            $this->chapelets->add($chapelet);
            $chapelet->setTypeCartes($this);
        }

        return $this;
    }

    public function removeChapelet(Chapelet $chapelet): static
    {
        if ($this->chapelets->removeElement($chapelet)) {
            // set the owning side to null (unless already changed)
            if ($chapelet->getTypeCartes() === $this) {
                $chapelet->setTypeCartes(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setTypeSim($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getTypeSim() === $this) {
                $commande->setTypeSim(null);
            }
        }

        return $this;
    }
    
}
