<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SAVRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ApiResource]
#[ORM\Entity(repositoryClass: SAVRepository::class)]
class SAV
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $materielNom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descriptionPanne = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'savs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMaterielNom(): ?string
    {
        return $this->materielNom;
    }

    public function setMaterielNom(string $materielNom): static
    {
        $this->materielNom = $materielNom;

        return $this;
    }

    public function getDescriptionPanne(): ?string
    {
        return $this->descriptionPanne;
    }

    public function setDescriptionPanne(string $descriptionPanne): static
    {
        $this->descriptionPanne = $descriptionPanne;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
