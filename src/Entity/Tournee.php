<?php

namespace App\Entity;

use App\Repository\TourneeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TourneeRepository::class)]
class Tournee
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer')]
	private ?int $id = null;

	#[ORM\ManyToOne(targetEntity: Commercial::class, inversedBy: 'tournees')]
	#[ORM\JoinColumn(nullable: false)]
	private ?Commercial $commercial = null;

	#[ORM\ManyToOne(targetEntity: Zone::class)]
	#[ORM\JoinColumn(nullable: false)]
	private ?Zone $zone = null;

	#[ORM\OneToMany(targetEntity: User::class, mappedBy: 'tournee')]
	private Collection $clients;

	#[ORM\Column(type: 'boolean')]
	private ?bool $completed = false;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

	public function __construct()
	{
		$this->clients = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getCommercial(): ?Commercial
	{
		return $this->commercial;
	}

	public function setCommercial(?Commercial $commercial): self
	{
		$this->commercial = $commercial;

		return $this;
	}

	public function getZone(): ?Zone
	{
		return $this->zone;
	}

	public function setZone(?Zone $zone): self
	{
		$this->zone = $zone;

		return $this;
	}

	/**
	 * @return Collection<int, User>
	 */
	public function getClients(): Collection
	{
		return $this->clients;
	}

	public function addClient(User $client): self
	{
		if (!$this->clients->contains($client)) {
			$this->clients->add($client);
			$client->setTournee($this); // Définir la tournée sur l'utilisateur
		}

		return $this;
	}

	public function removeClient(User $client): self
	{
		if ($this->clients->removeElement($client)) {
			// Set the owning side to null (unless already changed)
			if ($client->getTournee() === $this) {
				$client->setTournee(null);
			}
		}

		return $this;
	}

	public function isCompleted(): ?bool
	{
		return $this->completed;
	}

	public function setCompleted(bool $completed): self
	{
		$this->completed = $completed;

		return $this;
	}

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
