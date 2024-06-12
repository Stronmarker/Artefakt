<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RenderingController extends AbstractController
{
     #[Route('/rendering', name: 'app_rendering')]
    public function index(): Response
    {
        return $this->render('rendering/index.html.twig');
    }
}

