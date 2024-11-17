<?php

namespace App\Entity;

use App\Repository\SymaBusinessConfigRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SymaBusinessConfigRepository::class)]
class SymaBusinessConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nomDuResponsable = null;

    #[ORM\Column(length: 50)]
    private ?string $email = null;

    #[ORM\Column(length: 50)]
    private ?string $numeroDeTelephone = null;

    #[ORM\Column(length: 50)]
    private ?string $numeroDeFixe = null;

    #[ORM\Column(length: 50)]
    private ?string $raisonSociale = null;

    #[ORM\Column(length: 50)]
    private ?string $numeroDeRegistre = null;

    #[ORM\Column(length: 50)]
    private ?string $formeJuridique = null;

    #[ORM\Column(length: 50)]
    private ?string $codeApeNaf = null;

    #[ORM\Column(length: 50)]
    private ?string $capitalSocial = null;

    #[ORM\Column(length: 50)]
    private ?string $numeroDeTvaIntracommunautaire = null;

    #[ORM\Column(length: 50)]
    private ?string $numeroSiret = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $adresse = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomDuResponsable(): ?string
    {
        return $this->nomDuResponsable;
    }

    public function setNomDuResponsable(string $nomDuResponsable): static
    {
        $this->nomDuResponsable = $nomDuResponsable;

        return $this;
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

    public function getNumeroDeTelephone(): ?string
    {
        return $this->numeroDeTelephone;
    }

    public function setNumeroDeTelephone(string $numeroDeTelephone): static
    {
        $this->numeroDeTelephone = $numeroDeTelephone;

        return $this;
    }

    public function getNumeroDeFixe(): ?string
    {
        return $this->numeroDeFixe;
    }

    public function setNumeroDeFixe(string $numeroDeFixe): static
    {
        $this->numeroDeFixe = $numeroDeFixe;

        return $this;
    }

    public function getRaisonSociale(): ?string
    {
        return $this->raisonSociale;
    }

    public function setRaisonSociale(string $raisonSociale): static
    {
        $this->raisonSociale = $raisonSociale;

        return $this;
    }

    public function getNumeroDeRegistre(): ?string
    {
        return $this->numeroDeRegistre;
    }

    public function setNumeroDeRegistre(string $numeroDeRegistre): static
    {
        $this->numeroDeRegistre = $numeroDeRegistre;

        return $this;
    }

    public function getFormeJuridique(): ?string
    {
        return $this->formeJuridique;
    }

    public function setFormeJuridique(string $formeJuridique): static
    {
        $this->formeJuridique = $formeJuridique;

        return $this;
    }

    public function getCodeApeNaf(): ?string
    {
        return $this->codeApeNaf;
    }

    public function setCodeApeNaf(string $codeApeNaf): static
    {
        $this->codeApeNaf = $codeApeNaf;

        return $this;
    }

    public function getCapitalSocial(): ?string
    {
        return $this->capitalSocial;
    }

    public function setCapitalSocial(string $capitalSocial): static
    {
        $this->capitalSocial = $capitalSocial;

        return $this;
    }

    public function getNumeroDeTvaIntracommunautaire(): ?string
    {
        return $this->numeroDeTvaIntracommunautaire;
    }

    public function setNumeroDeTvaIntracommunautaire(string $numeroDeTvaIntracommunautaire): static
    {
        $this->numeroDeTvaIntracommunautaire = $numeroDeTvaIntracommunautaire;

        return $this;
    }

    public function getNumeroSiret(): ?string
    {
        return $this->numeroSiret;
    }

    public function setNumeroSiret(string $numeroSiret): static
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
}
