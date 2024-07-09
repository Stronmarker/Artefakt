<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserSubscriptionController extends AbstractController
{
    #[Route('/user/subscription', name: 'app_user_subscription')]
    public function index(): Response
    {
        
        return $this->render('user_subscription/index.html.twig', [
            'controller_name' => 'UserSubscriptionController',
        ]);
    }
}
