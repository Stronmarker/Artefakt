<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserSubscriptionController extends AbstractController
{
    #[Route('/user/subscription', name: 'app_user_subscription')]
    public function index(): Response
    {
        $user = $this->getUser();
        
        $template = $user && $user->isSubscribed() ? 'subscription/isSubscribed.html.twig' : 'subscription/isNotSubscribed.html.twig';

        return $this->render($template);
    }
}
