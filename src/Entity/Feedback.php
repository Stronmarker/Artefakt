<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\FeedbackRepository;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
class Feedback
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isAccepted;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(targetEntity: Rendering::class, inversedBy: 'feedbacks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Rendering $rendering = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsAccepted(): bool
    {
        return $this->isAccepted;
    }

    public function setIsAccepted(bool $isAccepted): self
    {
        $this->isAccepted = $isAccepted;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getRendering(): ?Rendering
    {
        return $this->rendering;
    }

    public function setRendering(?Rendering $rendering): self
    {
        $this->rendering = $rendering;

        return $this;
    }
}