<?php

namespace App\Entity;

use App\Repository\RenderingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RenderingRepository::class)]
class Rendering
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $frontPng = null;

    #[ORM\Column(length: 255)]
    private ?string $towardPng = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gildingSvg = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $laminationSvg = null;

    #[ORM\Column(length: 255)]
    private ?string $link = null;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'renderings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFrontPng(): ?string
    {
        return $this->frontPng;
    }

    public function setFrontPng(string $frontPng): self
    {
        $this->frontPng = $frontPng;

        return $this;
    }

    public function getTowardPng(): ?string
    {
        return $this->towardPng;
    }

    public function setTowardPng(string $towardPng): self
    {
        $this->towardPng = $towardPng;

        return $this;
    }

    public function getGildingSvg(): ?string
    {
        return $this->gildingSvg;
    }

    public function setGildingSvg(?string $gildingSvg): self
    {
        $this->gildingSvg = $gildingSvg;

        return $this;
    }

    public function getLaminationSvg(): ?string
    {
        return $this->laminationSvg;
    }

    public function setLaminationSvg(?string $laminationSvg): self
    {
        $this->laminationSvg = $laminationSvg;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

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
}
