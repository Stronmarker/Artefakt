<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RenderingRepository;
use Symfony\Component\HttpFoundation\File\File;
use Gedmo\Timestampable\Traits\TimestampableEntity;
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

    //#[Vich\UploadableField(mapping: 'renderings', fileNameProperty: 'gildingFrontPng')]
    //private ?File $gildingFrontPngFile = null;

    //#[ORM\Column(type: 'string', length: 255, nullable: true)]
    //private ?string $gildingFrontPng = null;

    //#[Vich\UploadableField(mapping: 'renderings', fileNameProperty: 'gildingTowardPng')]
    //private ?File $gildingTowardPngFile = null;

    //#[ORM\Column(type: 'string', length: 255, nullable: true)]
    //private ?string $gildingTowardPng = null;

    //#[Vich\UploadableField(mapping: 'renderings', fileNameProperty: 'laminationFrontPng')]
    //private ?File $laminationFrontPngFile = null;

    //#[ORM\Column(type: 'string', length: 255, nullable: true)]
    //private ?string $laminationFrontPng = null;

    //#[Vich\UploadableField(mapping: 'renderings', fileNameProperty: 'laminationTowardPng')]
    //private ?File $laminationTowardPngFile = null;

    //#[ORM\Column(type: 'string', length: 255, nullable: true)]
    //private ?string $laminationTowardPng = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $dimensions = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $link = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $renderingName = null; 

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'renderings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    public function __construct() {
    $this->createdAt = new \DateTimeImmutable() ;
    }

    // Getters et Setters pour toutes les propriétés


    public function setfrontPngFile(?File $imageFile = null): void
    {
        $this->frontPngFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getfrontPngFile(): ?File
    {
        return $this->frontPngFile;
    }

    public function settowardPngFile(?File $imageFile = null): void
    {
        $this->towardPngFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function gettowardPngFile(): ?File
    {
        return $this->towardPngFile;
    }




    // public function setgildingfrontPngFile(?File $imageFile = null): void
    // {
    //     $this->gildingFrontPngFile = $imageFile;

    //     if (null !== $imageFile) {
    //         // It is required that at least one field changes if you are using doctrine
    //         // otherwise the event listeners won't be called and the file is lost
    //         $this->updatedAt = new \DateTimeImmutable();
    //     }
    // }

    // public function getgildingfrontPngFile(): ?File
    // {
    //     return $this->gildingFrontPngFile;
    // }

    // public function setgildingtowardPngFile(?File $imageFile = null): void
    // {
    //     $this->gildingtowardPngFile = $imageFile;

    //     if (null !== $imageFile) {
    //         // It is required that at least one field changes if you are using doctrine
    //         // otherwise the event listeners won't be called and the file is lost
    //         $this->updatedAt = new \DateTimeImmutable();
    //     }
    // }

    // public function getgildingtowardPngFile(): ?File
    // {
    //     return $this->gildingTowardPngFile;
    // }



    // public function setlaminationFrontPngFile(?File $imageFile = null): void
    // {
    //     $this->laminationFrontPngFile = $imageFile;

    //     if (null !== $imageFile) {
    //         // It is required that at least one field changes if you are using doctrine
    //         // otherwise the event listeners won't be called and the file is lost
    //         $this->updatedAt = new \DateTimeImmutable();
    //     }
    // }

    // public function getlaminationFrontPngFile(): ?File
    // {
    //     return $this->laminationFrontPngFile;
    // }

    // public function setlaminationTowardPngFile(?File $imageFile = null): void
    // {
    //     $this->laminationTowardPngFile = $imageFile;

    //     if (null !== $imageFile) {
    //         // It is required that at least one field changes if you are using doctrine
    //         // otherwise the event listeners won't be called and the file is lost
    //         $this->updatedAt = new \DateTimeImmutable();
    //     }
    // }

    // public function getlaminationTowardPngFile(): ?File
    // {
    //     return $this->laminationTowardPngFile;
    // }

    
    

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

    // public function getGildingFrontPng(): ?string
    // {
    //     return $this->gildingFrontPng;
    // }

    // public function setgildingFrontPng(?string $gildingFrontPng): self
    // {
    //     $this->gildingFrontPng = $gildingFrontPng;

    //     return $this;
    // }

    // public function getGildingTowardPng(): ?string
    // {
    //     return $this->gildingTowardPng;
    // }

    // public function setgildingTowardPng(?string $gildingTowardPng): self
    // {
    //     $this->gildingTowardPng = $gildingTowardPng;

    //     return $this;
    // }

    // public function getLaminationFrontPng(): ?string
    // {
    //     return $this->laminationFrontPng;
    // }

    // public function setlaminationFrontPng(?string $laminationFrontPng): self
    // {
    //     $this->laminationFrontPng = $laminationFrontPng;

    //     return $this;
    // }

    // public function getLaminationTowardPng(): ?string
    // {
    //     return $this->laminationTowardPng;
    // }

    // public function setlaminationTowardPng(?string $laminationTowardPng): self
    // {
    //     $this->laminationTowardPng = $laminationTowardPng;

    //     return $this;
    // }

    public function getDimensions(): ?string
    {
        return $this->dimensions;
    }

    public function setDimensions(string $dimensions): self
    {
        $this->dimensions = $dimensions;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

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
