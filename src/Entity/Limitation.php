<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`limitation`')]  // Mettez à jour le nom de la table
class Limitation
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column]
	private ?int $limite = null;  // Limite du quota (par exemple 100, 200, etc.)

	#[ORM\Column]
	private ?float $prix = null;

    #[ORM\ManyToOne]
    private ?SimType $typeCarte = null;  // Prix pour cette tranche

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getLimite(): ?int
	{
		return $this->limite;
	}

	public function setLimite(int $limite): static
	{
		$this->limite = $limite;

		return $this;
	}

	public function getPrix(): ?float
	{
		return $this->prix;
	}

	public function setPrix(float $prix): static
	{
		$this->prix = $prix;

		return $this;
	}

	public function __toString(): string
	{
		return "Limite: {$this->limite} - Prix: {$this->prix} €";
	}

    public function getTypeCarte(): ?SimType
    {
        return $this->typeCarte;
    }

    public function setTypeCarte(?SimType $typeCarte): static
    {
        $this->typeCarte = $typeCarte;

        return $this;
    }
}
