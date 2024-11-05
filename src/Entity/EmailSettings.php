<?php

namespace App\Entity;

use App\Repository\EmailSettingsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailSettingsRepository::class)]
class EmailSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $confirmationEmailSubject = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $confirmationEmailBody = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConfirmationEmailSubject(): ?string
    {
        return $this->confirmationEmailSubject;
    }

    public function setConfirmationEmailSubject(string $confirmationEmailSubject): static
    {
        $this->confirmationEmailSubject = $confirmationEmailSubject;

        return $this;
    }

    public function getConfirmationEmailBody(): ?string
    {
        return $this->confirmationEmailBody;
    }

    public function setConfirmationEmailBody(string $confirmationEmailBody): static
    {
        $this->confirmationEmailBody = $confirmationEmailBody;

        return $this;
    }
}
