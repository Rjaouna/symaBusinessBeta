<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
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
    private bool $isVerified = false;

    #[ORM\Column(length: 50)]
    private ?string $nomResponsable = null;

    #[ORM\Column(length: 10)]
    private ?string $telephoneFixe = null;

    #[ORM\Column(length: 10)]
    private ?string $telephoneMobile = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $nomSociete = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $formeJuridique = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $numeroRegistreCommerce = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $numeroSiret = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $numeroRCS = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $codeAPE = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $facade = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $kbis = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $pays = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $codePostal = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $codeClient = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $iban = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $bic = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Quota $quotas = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Usage $usages = null;

    /**
     * @var Collection<int, Commande>
     */
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'user')]
    private Collection $commandes;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Bonus $bonus = null;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
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

    public function getTelephoneFixe(): ?string
    {
        return $this->telephoneFixe;
    }

    public function setTelephoneFixe(string $telephoneFixe): static
    {
        $this->telephoneFixe = $telephoneFixe;

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

    public function getFormeJuridique(): ?string
    {
        return $this->formeJuridique;
    }

    public function setFormeJuridique(?string $formeJuridique): static
    {
        $this->formeJuridique = $formeJuridique;

        return $this;
    }

    public function getNumeroRegistreCommerce(): ?string
    {
        return $this->numeroRegistreCommerce;
    }

    public function setNumeroRegistreCommerce(?string $numeroRegistreCommerce): static
    {
        $this->numeroRegistreCommerce = $numeroRegistreCommerce;

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

    public function getNumeroRCS(): ?string
    {
        return $this->numeroRCS;
    }

    public function setNumeroRCS(?string $numeroRCS): static
    {
        $this->numeroRCS = $numeroRCS;

        return $this;
    }

    public function getCodeAPE(): ?string
    {
        return $this->codeAPE;
    }

    public function setCodeAPE(?string $codeAPE): static
    {
        $this->codeAPE = $codeAPE;

        return $this;
    }

    public function getFacade(): ?string
    {
        return $this->facade;
    }

    public function setFacade(?string $facade): static
    {
        $this->facade = $facade;

        return $this;
    }

    public function getKbis(): ?string
    {
        return $this->kbis;
    }

    public function setKbis(?string $kbis): static
    {
        $this->kbis = $kbis;

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

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(?string $codePostal): static
    {
        $this->codePostal = $codePostal;

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

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban(?string $iban): static
    {
        $this->iban = $iban;

        return $this;
    }

    public function getBic(): ?string
    {
        return $this->bic;
    }

    public function setBic(?string $bic): static
    {
        $this->bic = $bic;

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

    public function getUsages(): ?Usage
    {
        return $this->usages;
    }

    public function setUsages(?Usage $usages): static
    {
        $this->usages = $usages;

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

    public function getBonus(): ?Bonus
    {
        return $this->bonus;
    }

    public function setBonus(?Bonus $bonus): static
    {
        $this->bonus = $bonus;

        return $this;
    }
}
