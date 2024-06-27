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

        if ($user && $user->isSubscribed()) {
            $utilisateurEstAbonne = true;
            $abonnement = [
                'type' => 'Premium',
                'price' => '€19.99/mois',
                'features' => [
                    'Création de projets illimités',
                    'Outils illimités',
                    'Assistance par e-mail et téléphone',
                    'Accès au centre d\'aide'
                ],
                'status' => 'Actif'
            ];
        } else {
            $utilisateurEstAbonne = false;
            $abonnement = [
                'type' => 'Abonnement Gratuit',
                'price' => '€0/mois',
                'features' => [
                    'Création de 3 projets',
                    'Outils limités',
                    'Assistance par e-mail',
                    'Accès au centre d\'aide',
                    'Pour plus de fonctionnalités passez à l\'offre premium'
                ],
                'status' => 'Offre Premium non active'
            ];
        }

        return $this->render('user_subscription/index.html.twig', [
            'abonnement' => $abonnement,
            'utilisateur_est_abonne' => $utilisateurEstAbonne
        ]);
    }
}
