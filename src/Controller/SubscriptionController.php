<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Subscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class SubscriptionController extends AbstractController
{
    #[Route('/subscription', name: 'app_subscription')]
    public function index(UserInterface $user): Response
    {
        return $this->render('subscription/index.html.twig', [
            'isSubscribed' => $user->isSubscribed(),
            'public_key' => $user->isSubscribed() ? null : $_ENV['STRIPE_PUBLIC_KEY'],
            'user' => $user,
        ]);
    }



    #[Route('/subscribe', name: 'app_subscribe', methods: ['POST'])]
    public function subscribe(Request $request, EntityManagerInterface $em, UserInterface $user): Response
    {
        if ($user->isSubscribed()) {
            return $this->json(['success' => false, 'message' => 'Vous êtes déjà abonné']);
        }

        $data = json_decode($request->getContent(), true);

        Stripe::setApiKey($_ENV['STRIPE_API_KEY']);

        try {
            if ($user->getStripeCustomerId()) {
                $customer = Customer::retrieve($user->getStripeCustomerId());
            } else {
                $customer = Customer::create([
                    'email' => $data['email'],
                    'address' => [
                        'line1' => $data['address'],
                        'city' => $data['city'],
                        'postal_code' => $data['postal_code'],
                        'state' => $data['state'],
                        'country' => $data['country'],
                    ]
                ]);
                $user->setStripeCustomerId($customer->id);
                $em->persist($user);
            }

            $paymentMethod = PaymentMethod::retrieve($data['payment_method']);
            $paymentMethod->attach(['customer' => $customer->id]);

            Customer::update($customer->id, [
                'invoice_settings' => [
                    'default_payment_method' => $data['payment_method'],
                ],
                'address' => [
                    'line1' => $data['address'],
                    'city' => $data['city'],
                    'postal_code' => $data['postal_code'],
                    'state' => $data['state'],
                    'country' => $data['country'],
                ],
            ]);

            $subscription = Subscription::create([
                'customer' => $customer->id,
                'items' => [[
                    'price' => 'price_1PgSDBBogeNlsS43hKcPTbxL',
                ]],
                'expand' => ['latest_invoice.payment_intent'],
                'automatic_tax' => ['enabled' => true],
            ]);

            $user->setSubscribed(true);
            $user->setAddress($data['address']);
            $user->setCity($data['city']);
            $user->setPostalCode($data['postal_code']);
            $user->setState($data['state']);
            $user->setCountry($data['country']);
            $user->setStripeSubscriptionId($subscription->id);
            $em->flush();

            return $this->json(['success' => true, 'subscription' => $subscription]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    

    #[Route('/cancel-subscription', name: 'app_cancel_subscription', methods: ['POST'])]
    public function cancelSubscription(EntityManagerInterface $em, UserInterface $user): Response
    {
        try {
            Stripe::setApiKey($_ENV['STRIPE_API_KEY']);

            $subscriptionId = $user->getStripeSubscriptionId();

            if (!$subscriptionId) {
                return new Response('Aucun abonnement trouvé pour cet utilisateur.', Response::HTTP_BAD_REQUEST);
            }

            $subscription = Subscription::retrieve($subscriptionId);
            $subscription->cancel();

            $user->setSubscribed(false);
            $user->setStripeSubscriptionId(null);
            $em->flush();

            return new Response('Abonnement résilié avec succès.', Response::HTTP_OK);
        } catch (\Exception $e) {
            return new Response('Erreur lors de la résiliation de l\'abonnement : ' . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
?>
