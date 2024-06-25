<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class SubscriptionController extends AbstractController
{
    #[Route('/subscription', name: 'app_subscription')]
    public function index(UserInterface $user): Response
    {
        $isSubscribed = $user->isSubscribed();

        return $this->render('subscription/index.html.twig', [
            'isSubscribed' => $isSubscribed,
        ]);
    }

    #[Route('/subscribe', name: 'app_subscribe', methods: ['POST'])]
    public function subscribe(EntityManagerInterface $em, UserInterface $user): Response
    {
        if ($user->isSubscribed()) {
            return $this->json(['success' => false, 'message' => 'Vous êtes déjà abonné']);
        }

        $user->setSubscribed(true);
        $em->persist($user);
        $em->flush();

        return $this->json(['success' => true]);
    }
}
