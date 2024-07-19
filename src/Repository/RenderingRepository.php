<?php

namespace App\Repository;

use App\Entity\Rendering;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RenderingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rendering::class);
    }

    // Ajoutez ici des méthodes personnalisées de requête si nécessaire
}
