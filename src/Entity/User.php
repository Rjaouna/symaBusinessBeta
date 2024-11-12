<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]

#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cet email.')]
#[UniqueEntity(fields: ['codeClient'], message: 'Il existe déjà un compte avec ce code client.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups('user_info')]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    #[Groups('user_info')]
    private bool $isVerified = false;

    #[ORM\Column(length: 50)]
    #[Groups('user_info')]
    private ?string $nomResponsable = null;

   
    #[ORM\Column(length: 10)]
    #[Groups('user_info')]
    private ?string $telephoneMobile = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups('user_info')]
    private ?string $nomSociete = null;

   
  
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $numeroSiret = null;
    #[ORM\Column(length: 50, nullable: true)]
    #[Groups('user_info')]
    private ?string $adresse = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups('user_info')]
    private ?string $pays = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups('user_info')]
    private ?string $ville = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups('user_info')]
    private ?string $codeClient = null;

  
  
    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups('user_info')]
    private ?Quota $quotas = null;

    
    /**
     * @var Collection<int, Commande>
     */
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'user')]
    #[Groups('user_info')]
    private Collection $commandes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Bonus::class, cascade: ['persist', 'remove'])]
    #[Groups('user_info')]
    private Collection $bonuses;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups('user_info')]
    private ?string $totalBonus = null;

    #[ORM\Column]
    #[Groups('user_info')]
    private ?int $sim5Usage = null;

    #[ORM\Column]
    #[Groups('user_info')]
    private ?int $sim10Usage = null;

    #[ORM\Column]
    #[Groups('user_info')]
    private ?int $sim15Usage = null;

    #[ORM\Column]
    #[Groups('user_info')]
    private ?int $sim20Usage = null;

    /**
     * @var Collection<int, CarteSim>
     */
    #[ORM\OneToMany(targetEntity: CarteSim::class, mappedBy: 'user')]
    private Collection $carteSims; 

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->carteSims = new ArrayCollection();
        $this->factures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getNomResponsable(): ?string
    {
        return $this->nomResponsable;
    }

    public function setNomResponsable(string $nomResponsable): static
    {
        $this->nomResponsable = $nomResponsable;

        return $this;
    }



    public function getTelephoneMobile(): ?string
    {
        return $this->telephoneMobile;
    }

    public function setTelephoneMobile(string $telephoneMobile): static
    {
        $this->telephoneMobile = $telephoneMobile;

        return $this;
    }

    public function getNomSociete(): ?string
    {
        return $this->nomSociete;
    }

    public function setNomSociete(?string $nomSociete): static
    {
        $this->nomSociete = $nomSociete;

        return $this;
    }

 

    public function getNumeroSiret(): ?string
    {
        return $this->numeroSiret;
    }

    public function setNumeroSiret(?string $numeroSiret): static
    {
        $this->numeroSiret = $numeroSiret;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCodeClient(): ?string
    {
        return $this->codeClient;
    }

    public function setCodeClient(?string $codeClient): static
    {
        $this->codeClient = $codeClient;

        return $this;
    }

    public function getQuotas(): ?Quota
    {
        return $this->quotas;
    }

    public function setQuotas(?Quota $quotas): static
    {
        $this->quotas = $quotas;

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
            $commande->setUser($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getUser() === $this) {
                $commande->setUser(null);
            }
        }

        return $this;
    }



    public function __toString()
    {
        return $this->email;
    }

    public function getTotalBonus(): ?string
    {
        return $this->totalBonus;
    }

    public function setTotalBonus(?string $totalBonus): static
    {
        $this->totalBonus = $totalBonus;

        return $this;
    }

    public function getSim5Usage(): ?int
    {
        return $this->sim5Usage;
    }

    public function setSim5Usage(int $sim5Usage): static
    {
        $this->sim5Usage = $sim5Usage;

        return $this;
    }

    public function getSim10Usage(): ?int
    {
        return $this->sim10Usage;
    }

    public function setSim10Usage(int $sim10Usage): static
    {
        $this->sim10Usage = $sim10Usage;

        return $this;
    }

    public function getSim15Usage(): ?int
    {
        return $this->sim15Usage;
    }

    public function setSim15Usage(int $sim15Usage): static
    {
        $this->sim15Usage = $sim15Usage;

        return $this;
    }

    public function getSim20Usage(): ?int
    {
        return $this->sim20Usage;
    }

    public function setSim20Usage(int $sim20Usage): static
    {
        $this->sim20Usage = $sim20Usage;

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
            $carteSim->setUser($this);
        }

        return $this;
    }

    public function removeCarteSim(CarteSim $carteSim): static
    {
        if ($this->carteSims->removeElement($carteSim)) {
            // set the owning side to null (unless already changed)
            if ($carteSim->getUser() === $this) {
                $carteSim->setUser(null);
            }
        }

        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $activeRole = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastUpdatedQuotaAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $sim5Bonus = null;

    #[ORM\Column(nullable: true)]
    private ?int $sim10Bonus = null;

    #[ORM\Column(nullable: true)]
    private ?int $sim15Bonus = null;

    #[ORM\Column(nullable: true)]
    private ?int $sim20Bonus = null;

    /**
     * @var Collection<int, Facture>
     */
    #[ORM\OneToMany(targetEntity: Facture::class, mappedBy: 'client')]
    private Collection $factures;

    public function setActiveRole(string $role): self
    {
        $this->activeRole = $role;
        return $this;
    }

    public function getActiveRole(): ?string
    {
        return $this->activeRole;
    }

    public function getLastUpdatedQuotaAt(): ?\DateTimeImmutable
    {
        return $this->lastUpdatedQuotaAt;
    }

    public function setLastUpdatedQuotaAt(?\DateTimeImmutable $lastUpdatedQuotaAt): static
    {
        $this->lastUpdatedQuotaAt = $lastUpdatedQuotaAt;

        return $this;
    }

    public function getSim5Bonus(): ?int
    {
        return $this->sim5Bonus;
    }

    public function setSim5Bonus(?int $sim5Bonus): static
    {
        $this->sim5Bonus = $sim5Bonus;

        return $this;
    }

    public function getSim10Bonus(): ?int
    {
        return $this->sim10Bonus;
    }

    public function setSim10Bonus(?int $sim10Bonus): static
    {
        $this->sim10Bonus = $sim10Bonus;

        return $this;
    }

    public function getSim15Bonus(): ?int
    {
        return $this->sim15Bonus;
    }

    public function setSim15Bonus(?int $sim15Bonus): static
    {
        $this->sim15Bonus = $sim15Bonus;

        return $this;
    }

    public function getSim20Bonus(): ?int
    {
        return $this->sim20Bonus;
    }

    public function setSim20Bonus(?int $sim20Bonus): static
    {
        $this->sim20Bonus = $sim20Bonus;

        return $this;
    }

    /**
     * @return Collection<int, Facture>
     */
    public function getFactures(): Collection
    {
        return $this->factures;
    }

    public function addFacture(Facture $facture): static
    {
        if (!$this->factures->contains($facture)) {
            $this->factures->add($facture);
            $facture->setClient($this);
        }

        return $this;
    }

    public function removeFacture(Facture $facture): static
    {
        if ($this->factures->removeElement($facture)) {
            // set the owning side to null (unless already changed)
            if ($facture->getClient() === $this) {
                $facture->setClient(null);
            }
        }

        return $this;
    }
}
