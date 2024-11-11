<?php

namespace App\Entity;

use App\Repository\PendingSimCardsRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[UniqueEntity(fields: ['serialNumber'], message: "Ce numéro de série doit être unique.")]
#[ORM\Entity(repositoryClass: PendingSimCardsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class PendingSimCards
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 19)]
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
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?bool $migrated = null;

    #[ORM\ManyToOne(inversedBy: 'pendingSimCards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SimType $type = null;

    #[ORM\Column]
    private ?bool $importedCsv = null;

    #[ORM\ManyToOne(inversedBy: 'pendingSimCards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Chapelet $chapelet = null;

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

    public function isMigrated(): ?bool
    {
        return $this->migrated;
    }

    public function setMigrated(bool $migrated): static
    {
        $this->migrated = $migrated;

        return $this;
    }
    #[ORM\PrePersist]
    public function onPrePersist()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->migrated = false;
    }
    #[ORM\PreUpdate]
    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTimeImmutable();
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

    public function isImportedCsv(): ?bool
    {
        return $this->importedCsv;
    }

    public function setImportedCsv(bool $importedCsv): static
    {
        $this->importedCsv = $importedCsv;

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
    
}
