<?php

namespace App\Entity;

use App\Repository\ChapeletRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: ChapeletRepository::class)]
class Chapelet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 14, unique: true)]
    private ?string $codeChapelet = null;

    /**
     * @var Collection<int, CarteSim>
     */
    #[ORM\OneToMany(targetEntity: CarteSim::class, mappedBy: 'chapelet')]
    private Collection $cartesSims;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, PendingSimCards>
     */
    #[ORM\OneToMany(targetEntity: PendingSimCards::class, mappedBy: 'chapelet')]
    private Collection $pendingSimCards;

    #[ORM\Column]
    private ?bool $reserved = null;

    #[ORM\ManyToOne(inversedBy: 'chapelets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SimType $typeCartes = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $soldBy = null;

    public function __construct()
    {
        $this->cartesSims = new ArrayCollection();
        $this->pendingSimCards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeChapelet(): ?string
    {
        return $this->codeChapelet;
    }

    public function setCodeChapelet(string $codeChapelet): static
    {
        $this->codeChapelet = $codeChapelet;

        return $this;
    }

    /**
     * @return Collection<int, CarteSim>
     */
    public function getCartesSims(): Collection
    {
        return $this->cartesSims;
    }

    public function addCartesSim(CarteSim $cartesSim): static
    {
        if (!$this->cartesSims->contains($cartesSim)) {
            $this->cartesSims->add($cartesSim);
            $cartesSim->setChapelet($this);
        }

        return $this;
    }

    public function removeCartesSim(CarteSim $cartesSim): static
    {
        if ($this->cartesSims->removeElement($cartesSim)) {
            // set the owning side to null (unless already changed)
            if ($cartesSim->getChapelet() === $this) {
                $cartesSim->setChapelet(null);
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
    public function onPrePersist(): void
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
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
            $pendingSimCard->setChapelet($this);
        }

        return $this;
    }

    public function removePendingSimCard(PendingSimCards $pendingSimCard): static
    {
        if ($this->pendingSimCards->removeElement($pendingSimCard)) {
            // set the owning side to null (unless already changed)
            if ($pendingSimCard->getChapelet() === $this) {
                $pendingSimCard->setChapelet(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (string)$this->codeChapelet;
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

    public function getTypeCartes(): ?SimType
    {
        return $this->typeCartes;
    }

    public function setTypeCartes(?SimType $typeCartes): static
    {
        $this->typeCartes = $typeCartes;

        return $this;
    }

    public function getSoldBy(): ?string
    {
        return $this->soldBy;
    }

    public function setSoldBy(?string $soldBy): static
    {
        $this->soldBy = $soldBy;

        return $this;
    }
}
