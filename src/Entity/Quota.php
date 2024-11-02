<?php

namespace App\Entity;

use App\Repository\QuotaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: QuotaRepository::class)]
class Quota
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups('user_info')]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    #[Groups('user_info')]
    private ?string $sim5Quota = null;

    #[ORM\Column(length: 50)]
    #[Groups('user_info')]
    private ?string $sim10Quota = null;

    #[ORM\Column(length: 50)]
    #[Groups('user_info')]
    private ?string $sim15Quota = null;

    #[ORM\Column(length: 50)]
    #[Groups('user_info')]
    private ?string $sim20Quota = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'quotas')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getSim5Quota(): ?string
    {
        return $this->sim5Quota;
    }

    public function setSim5Quota(string $sim5Quota): static
    {
        $this->sim5Quota = $sim5Quota;

        return $this;
    }

    public function getSim10Quota(): ?string
    {
        return $this->sim10Quota;
    }

    public function setSim10Quota(string $sim10Quota): static
    {
        $this->sim10Quota = $sim10Quota;

        return $this;
    }

    public function getSim15Quota(): ?string
    {
        return $this->sim15Quota;
    }

    public function setSim15Quota(string $sim15Quota): static
    {
        $this->sim15Quota = $sim15Quota;

        return $this;
    }

    public function getSim20Quota(): ?string
    {
        return $this->sim20Quota;
    }

    public function setSim20Quota(string $sim20Quota): static
    {
        $this->sim20Quota = $sim20Quota;

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
    public function onPrePersist()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();

    }
    #[ORM\PreUpdate]
    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setQuotas($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getQuotas() === $this) {
                $user->setQuotas(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nom;
    }
}
