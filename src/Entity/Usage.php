<?php

namespace App\Entity;

use App\Repository\UsageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsageRepository::class)]
#[ORM\Table(name: '`usage`')]
class Usage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $sim5Usage = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $sim10Usage = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $sim15Usage = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $sim20Usage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSim5Usage(): ?string
    {
        return $this->sim5Usage;
    }

    public function setSim5Usage(string $sim5Usage): static
    {
        $this->sim5Usage = $sim5Usage;

        return $this;
    }

    public function getSim10Usage(): ?string
    {
        return $this->sim10Usage;
    }

    public function setSim10Usage(string $sim10Usage): static
    {
        $this->sim10Usage = $sim10Usage;

        return $this;
    }

    public function getSim15Usage(): ?string
    {
        return $this->sim15Usage;
    }

    public function setSim15Usage(string $sim15Usage): static
    {
        $this->sim15Usage = $sim15Usage;

        return $this;
    }

    public function getSim20Usage(): ?string
    {
        return $this->sim20Usage;
    }

    public function setSim20Usage(string $sim20Usage): static
    {
        $this->sim20Usage = $sim20Usage;

        return $this;
    }
}
