<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\StripeClient;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    #[Route('/invoices', name: 'app_invoices')]
    public function viewInvoices(UserInterface $user, EntityManagerInterface $em): Response
    {
       
        $stripe = new StripeClient($_ENV['STRIPE_API_KEY']);
        $invoices = $stripe->invoices->all(['customer' => $user->getStripeCustomerId()]);
        dd($invoices);


        return $this->render('dashboard/invoices.html.twig', [
            'invoices' => $invoices,
        ]);
    }

}
