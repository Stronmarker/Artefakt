<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RenderingRepository;
use Symfony\Component\HttpFoundation\File\File;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: RenderingRepository::class)]
#[Vich\Uploadable]
class Rendering
{
    use TimestampableEntity;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: 'renderings', fileNameProperty: 'frontPng')]
    private ?File $frontPngFile = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $frontPng = null;

    #[Vich\UploadableField(mapping: 'renderings', fileNameProperty: 'towardPng')]
    private ?File $towardPngFile = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $towardPng = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $renderingName = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $token = null;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'renderings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[ORM\OneToMany(mappedBy: 'rendering', targetEntity: Feedback::class, cascade: ['persist', 'remove'])]
    private Collection $feedbacks;

    public function __construct()
    {
        $this->feedbacks = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFrontPng(): ?string
    {
        return $this->frontPng;
    }

    public function setFrontPng(?string $frontPng): self
    {
        $this->frontPng = $frontPng;

        return $this;
    }

    public function getFrontPngFile(): ?File
    {
        return $this->frontPngFile;
    }

    public function setFrontPngFile(?File $frontPngFile = null): self
    {
        $this->frontPngFile = $frontPngFile;

        if (null !== $frontPngFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getTowardPng(): ?string
    {
        return $this->towardPng;
    }

    public function setTowardPng(?string $towardPng): self
    {
        $this->towardPng = $towardPng;

        return $this;
    }

    public function getTowardPngFile(): ?File
    {
        return $this->towardPngFile;
    }

    public function setTowardPngFile(?File $towardPngFile = null): self
    {
        $this->towardPngFile = $towardPngFile;

        if (null !== $towardPngFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getRenderingName(): ?string
    {
        return $this->renderingName;
    }

    public function setRenderingName(string $renderingName): self
    {
        $this->renderingName = $renderingName;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return Collection|Feedback[]
     */
    public function getFeedbacks(): Collection
    {
        return $this->feedbacks;
    }

    public function addFeedback(Feedback $feedback): self
    {
        if (!$this->feedbacks->contains($feedback)) {
            $this->feedbacks[] = $feedback;
            $feedback->setRendering($this);
        }

        return $this;
    }

    public function removeFeedback(Feedback $feedback): self
    {
        if ($this->feedbacks->removeElement($feedback)) {
            // set the owning side to null (unless already changed)
            if ($feedback->getRendering() === $this) {
                $feedback->setRendering(null);
            }
        }

        return $this;
    }
}