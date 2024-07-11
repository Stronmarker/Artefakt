<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{

    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $projectName = null;

    #[ORM\Column(name: 'client_name', length: 255, nullable: true)]
    private ?string $clientName = null;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Rendering::class, cascade: ['persist', 'remove'])]
    private Collection $renderings;

    #[ORM\ManyToOne(inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    public function __construct()
    {
        $this->renderings = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable() ;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectName(): ?string
    {
        return $this->projectName;
    }

    public function setProjectName(string $projectName): self
    {
        $this->projectName = $projectName;

        return $this;
    }

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function setClientName(?string $clientName): self
    {
        $this->clientName = $clientName;

        return $this;
    }

    /**
     * @return Collection<int, Rendering>
     */
    public function getRenderings(): Collection
    {
        return $this->renderings;
    }

    public function addRendering(Rendering $rendering): self
    {
        if (!$this->renderings->contains($rendering)) {
            $this->renderings[] = $rendering;
            $rendering->setProject($this);
        }

        return $this;
    }

    public function removeRendering(Rendering $rendering): self
    {
        if ($this->renderings->removeElement($rendering)) {
            if ($rendering->getProject() === $this) {
                $rendering->setProject(null);
            }
        }

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
