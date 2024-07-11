<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface; // Importation du gestionnaire d'entités de Doctrine
use Stripe\Stripe; // Importation de la classe Stripe
use Stripe\Customer; // Importation de la classe Customer de Stripe
use Stripe\Subscription; // Importation de la classe Subscription de Stripe
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Importation de la classe de base des contrôleurs de Symfony
use Symfony\Component\HttpFoundation\Request; // Importation de la classe Request de Symfony
use Symfony\Component\HttpFoundation\Response; // Importation de la classe Response de Symfony
use Symfony\Component\Routing\Annotation\Route; // Importation de la classe Route pour les annotations de routage
use Symfony\Component\Security\Core\User\UserInterface; // Importation de l'interface UserInterface pour représenter l'utilisateur

class SubscriptionController extends AbstractController
{
    // Route pour la page d'abonnement, méthode HTTP GET par défaut
    #[Route('/subscription', name: 'app_subscription')]
    public function index(UserInterface $user): Response
    {
        // Vérifie si l'utilisateur est déjà abonné
        $isSubscribed = $user->isSubscribed();

        // Rend la vue subscription/index.html.twig avec les variables nécessaires
        return $this->render('subscription/index.html.twig', [
            'isSubscribed' => $isSubscribed,
            'public_key' => $isSubscribed ? null : $_ENV['STRIPE_PUBLIC_KEY'], // Clé publique de Stripe
        ]);
    }

    // Route pour gérer l'abonnement, méthode HTTP POST uniquement
    #[Route('/subscribe', name: 'app_subscribe', methods: ['POST'])]
    public function subscribe(Request $request, EntityManagerInterface $em, UserInterface $user): Response
    {
        // Vérifie si l'utilisateur est déjà abonné
        if ($user->isSubscribed()) {
            return $this->json(['success' => false, 'message' => 'Vous êtes déjà abonné']);
        }

        // Récupère les données JSON de la requête
        $data = json_decode($request->getContent(), true);
        $email = $data['email']; // Email de l'utilisateur
        $paymentMethod = $data['payment_method']; // Méthode de paiement

        // Définit la clé secrète de l'API Stripe
        Stripe::setApiKey($_ENV['STRIPE_API_KEY']); // Clé secrète de Stripe

        try {
            // Vérifie si l'utilisateur a déjà un client Stripe
            if ($user->getStripeCustomerId()) {
                // Utilise le client Stripe existant
                $customerId = $user->getStripeCustomerId();
                $customer = Customer::retrieve($customerId);
                $customer->invoice_settings = ['default_payment_method' => $paymentMethod];
                $customer->save();
            } else {
                // Crée un nouveau client Stripe
                $customer = Customer::create([
                    'email' => $email,
                    'payment_method' => $paymentMethod,
                    'invoice_settings' => [
                        'default_payment_method' => $paymentMethod,
                    ],
                ]);
                $user->setStripeCustomerId($customer->id);
                $em->persist($user); // Persiste l'utilisateur avec le Stripe ID
            }

            // Crée un nouvel abonnement pour le client
            $subscription = Subscription::create([
                'customer' => $customer->id,
                'items' => [[
                    'price' => 'price_1Pab22B8NXWXURY9vaFDcA0n', // ID du plan de paiement créé sur Stripe
                ]],
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            // Marque l'utilisateur comme abonné dans la base de données
            $user->setSubscribed(true);
            $em->flush(); // Sauvegarde les changements dans la base de données

            // Retourne une réponse JSON avec succès
            return $this->json(['success' => true, 'subscription' => $subscription]);
        } catch (\Exception $e) {
            // En cas d'erreur, retourne une réponse JSON avec le message d'erreur
            return $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
?>
