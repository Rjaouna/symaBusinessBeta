<?php

namespace App\Entity;

use App\Repository\CommercialRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommercialRepository::class)]
class Commercial
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $email = null;
    // Nouveau champ booléen
    #[ORM\Column(type: 'boolean')]
    private bool $isCommercial = true; // Valeur par défaut à true
    // Getter pour isCommercial
    public function isCommercial(): bool
    {
        return $this->isCommercial;
    }

    // Setter pour isCommercial
    public function setIsCommercial(bool $isCommercial): self
    {
        $this->isCommercial = $isCommercial;

        return $this;
    }
    #[ORM\OneToMany(mappedBy: 'commercial', targetEntity: Tournee::class)]
    private Collection $tournees;

    public function __construct()
    {
        $this->tournees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

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
     * @return Collection<int, Tournee>
     */
    public function getTournees(): Collection
    {
        return $this->tournees;
    }

    public function addTournee(Tournee $tournee): self
    {
        if (!$this->tournees->contains($tournee)) {
            $this->tournees[] = $tournee;
            $tournee->setCommercial($this);
        }

        return $this;
    }

    public function removeTournee(Tournee $tournee): self
    {
        if ($this->tournees->removeElement($tournee)) {
            // set the owning side to null (unless already changed)
            if ($tournee->getCommercial() === $this) {
                $tournee->setCommercial(null);
            }
        }

        return $this;
    }
}
