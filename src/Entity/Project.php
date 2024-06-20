<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $project_name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $creation_date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $modification_date = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Client $client = null;

    #[ORM\Column(length: 255)]
    private ?string $client_name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectName(): ?string
    {
        return $this->project_name;
    }

    public function setProjectName(string $project_name): static
    {
        $this->project_name = $project_name;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creation_date;
    }

    public function setCreationDate(\DateTimeInterface $creation_date): static
    {
        $this->creation_date = $creation_date;

        return $this;
    }

    public function getModificationDate(): ?\DateTimeInterface
    {
        return $this->modification_date;
    }

    public function setModificationDate(\DateTimeInterface $modification_date): static
    {
        $this->modification_date = $modification_date;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;
        $this->client_name = $client ? $client->getName() : null;

        return $this;
    }

    public function getClientName(): ?string
    {
        return $this->client_name;
    }

    public function setClientName(string $client_name): static
    {
        $this->client_name = $client_name;

        return $this;
    }
}
