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
            'public_key' => 'pk_test_51N1m7HB8NXWXURY9QWbBOLT9JC5tPWtrBDyfJEO3u6A6owTH8Yu3daMvDoMvmo6fGfQFzkTcAMCb9cfum1cTp51W000XeNlvkv', // Clé publique de Stripe
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
        Stripe::setApiKey('sk_test_51N1m7HB8NXWXURY9cnq0pT8MVnBwb4s4mUaW3D1z3Pel7jwYQjfsezlh3SXVdfo3Ic4fVej3bdC2P8euKhUpHq8R00FrPw8ACR'); // Clé secrète de Stripe

        try {
            // Crée un nouveau client Stripe
            $customer = Customer::create([
                'email' => $email,
                'payment_method' => $paymentMethod,
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethod,
                ],
            ]);

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
            $em->persist($user); // Persiste l'utilisateur dans l'EntityManager
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
