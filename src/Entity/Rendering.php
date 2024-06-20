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
    private ?string $front_png = null;

    #[ORM\Column(length: 255)]
    private ?string $toward_png = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gilding_svg = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $toward_svg = null;

    #[ORM\Column(length: 255)]
    private ?string $link = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFrontPng(): ?string
    {
        return $this->front_png;
    }

    public function setFrontPng(string $front_png): static
    {
        $this->front_png = $front_png;

        return $this;
    }

    public function getTowardPng(): ?string
    {
        return $this->toward_png;
    }

    public function setTowardPng(string $toward_png): static
    {
        $this->toward_png = $toward_png;

        return $this;
    }

    public function getGildingSvg(): ?string
    {
        return $this->gilding_svg;
    }

    public function setGildingSvg(?string $gilding_svg): static
    {
        $this->gilding_svg = $gilding_svg;

        return $this;
    }

    public function getTowardSvg(): ?string
    {
        return $this->toward_svg;
    }

    public function setTowardSvg(?string $toward_svg): static
    {
        $this->toward_svg = $toward_svg;

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
