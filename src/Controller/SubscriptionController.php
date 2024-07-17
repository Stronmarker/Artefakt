<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface; // Importation de l'entité interface manager de doctrine
use Stripe\Stripe; // Importation de la classe stripe
use Stripe\Customer; // Importation de la classe customer de stripe
use Stripe\PaymentMethod; // Importation de la classe paymentMethod de stripe
use Stripe\Subscription; // Importation de la classe subscription de stripe
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Importation de la classe de base des contrôleurs de Symfony
use Symfony\Component\HttpFoundation\Request; // Importation de la classe Request de Symfony
use Symfony\Component\HttpFoundation\Response; // Importation de la classe Response de Symfony
use Symfony\Component\Routing\Annotation\Route; // Importation de la classe Route pour les annotations de routage
use Symfony\Component\Security\Core\User\UserInterface; // Importation de l'interface UserInterface pour représenter l'utilisateur

class SubscriptionController extends AbstractController
{
    // Route pour afficher la page d'abonnement, méthode HTTP GET par défaut
    #[Route('/subscription', name: 'app_subscription')]
    public function index(UserInterface $user): Response
    {
        // Vérification si l'utilisateur est déjà abonné
        $isSubscribed = $user->isSubscribed();

        // Rend la vue subscription/index.html.twig avec les variables nécessaires
        return $this->render('subscription/index.html.twig', [
            'isSubscribed' => $isSubscribed, // Indication si l'utilisateur est abonné
            'public_key' => $isSubscribed ? null : $_ENV['STRIPE_PUBLIC_KEY'], // Clé public de stripe
        ]);
    }

    // Route qui gére l'abonnement avec une méthode POST uniquement
    #[Route('/subscribe', name: 'app_subscribe', methods: ['POST'])]
    public function subscribe(Request $request, EntityManagerInterface $em, UserInterface $user): Response
    {
        // Vérification si l'utilisateur est déjà abonné en lui affichant une sweetAlert lui indiquant un message définit
        if ($user->isSubscribed()) {
            return $this->json(['success' => false, 'message' => 'Vous êtes déjà abonné']);
        }

        // Récupère les données de la requête JSON
        $data = json_decode($request->getContent(), true);
        $email = $data['email']; // Email de l'utilisateur
        $address = $data['address'];
        $city = $data['city'];
        $postalCode = $data['postal_code'];
        $state = $data['state'];
        $country = $data['country'];
        $paymentMethodId = $data['payment_method']; //méthode de paiement stripe

        Stripe::setApiKey($_ENV['STRIPE_API_KEY']); // Configuration de la clé privé de l'API stripe

        try {


            if ($user->getStripeCustomerId()) { // Vérifie si l'utilisateur a déjà un client Stripe

                // Récupère le client Stripe existant
                $customerId = $user->getStripeCustomerId();
                $customer = Customer::retrieve($customerId);
            } else {

                // Création d'un nouveau client stripe
                $customer = Customer::create([
                    'email' => $email,
                    'address' => [
                        'line1' => $address,
                        'city' => $city,
                        'postal_code' => $postalCode,
                        'state' => $state,
                        'country' => $country,
                    ]
                ]);
                $user->setStripeCustomerId($customer->id);// Associe l'ID client Stripe à l'utilisateur
                $em->persist($user); // Persiste l'utilisateur avec le Stripe ID
            }

            // Récupère et attache la méthode de paiement au client Stripe
            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
            $paymentMethod->attach(['customer' => $customer->id]);

            // Met à jour les paramètres de facturation du client pour utiliser la méthode de paiement par défaut
            Customer::update($customer->id, [
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethodId,
                ],
            'address' => [
                'line1' => $address,
                'city' => $city,
                'postal_code' => $postalCode,
                'state' => $state,
                'country' => $country,
                ],
            ]);

            // Crée un nouvel abonnement pour le client
            $subscription = Subscription::create([
                'customer' => $customer->id,
                'items' => [[
                    'price' => 'price_1Pab22B8NXWXURY9vaFDcA0n', // Id du plan de paiement crée sur stripe
                ]],
                'expand' => ['latest_invoice.payment_intent'], // Étend pour inclure l'objet payment_intent dans la dernière facture
            ]);

            // Marque l'utilisateur comme abonné dans la base de données
            $user->setSubscribed(true);
            $user->setAddress($address);
            $user->setCity($city);
            $user->setPostalCode($postalCode);
            $user->setState($state);
            $user->setCountry($country);
            $user->setStripeSubscriptionId($subscription->id);
            $em->flush(); // Sauvegarde les changements dans la base de données

             // Retourne une réponse JSON avec succès
            return $this->json(['success' => true, 'subscription' => $subscription]);
        } catch (\Exception $e) {
            // En cas d'erreur, retourne une réponse JSON avec le message d'erreur
            return $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    #[Route('/cancel-subscription', name: 'app_cancel_subscription', methods: ['POST'])]
    public function cancelSubscription(Request $request, EntityManagerInterface $em, UserInterface $user): Response
    {
        try {
            Stripe::setApiKey($_ENV['STRIPE_API_KEY']);

            $subscriptionId = $user->getStripeSubscriptionId();

            if (!$subscriptionId) {
                return new Response(['Aucun abonnement trouvé pour cet utilisateur.', Response::HTTP_BAD_REQUEST]);
            }

            // Récupère l'abonnement Stripe
            $subscription = Subscription::retrieve($subscriptionId);
            $subscription->cancel(); // Annulation de l'abonnement dans Stripe

            // Mettre à jour l'état d'abonnement dans la base de données
            $user->setSubscribed(false);
            $user->setStripeSubscriptionId(null);
            $em->flush();

            return new Response('Abonnement résilié avec succès.', Response::HTTP_OK);
        } catch (ApiErrorException $e) {
            return new Response('Erreur lors de la résiliation de l\'abonnement : ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

}
?>
