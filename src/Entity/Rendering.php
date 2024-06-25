<?php

namespace App\Entity;

use App\Repository\RenderingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RenderingRepository::class)]
class Rendering
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFrontPng(): ?string
    {
        return $this->frontPng;
    }

    public function setFrontPng(string $frontPng): static
    {
        $this->frontPng = $frontPng;

        return $this;
    }

    public function getTowardPng(): ?string
    {
        return $this->towardPng;
    }

    public function setTowardPng(string $towardPng): static
    {
        $this->towardPng = $towardPng;

        return $this;
    }

    public function getGildingSvg(): ?string
    {
        return $this->gildingSvg;
    }

    public function setGildingSvg(?string $gildingSvg): static
    {
        $this->gildingSvg = $gildingSvg;

        return $this;
    }

    public function getTowardSvg(): ?string
    {
        return $this->laminationSvg;
    }

    public function setTowardSvg(?string $laminationSvg): static
    {
        $this->laminationSvg = $laminationSvg;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;

        return $this;
    }
}
