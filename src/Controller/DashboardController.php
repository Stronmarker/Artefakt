<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Stripe\Stripe;
use Stripe\BillingPortal\Session;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    #[Route('/billing-portal', name: 'app_billing_portal')]
    public function billingPortal(UserInterface $user): Response
    {
        Stripe::setApiKey($_ENV['STRIPE_API_KEY']);

        try {
            $session = Session::create([
                'customer' => $user->getStripeCustomerId(),
                'return_url' => $this->generateUrl('app_dashboard', [], 0),
            ]);

            return $this->redirect($session->url);
        } catch (\Exception $e) {
            // En cas d'erreur, retourne une réponse JSON avec le message d'erreur
            return $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
