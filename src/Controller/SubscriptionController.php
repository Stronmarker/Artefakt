<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Customer;
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
        $isSubscribed = $user->isSubscribed();

        return $this->render('subscription/index.html.twig', [
            'isSubscribed' => $isSubscribed,
            'public_key' => 'pk_test_51N1m7HB8NXWXURY9QWbBOLT9JC5tPWtrBDyfJEO3u6A6owTH8Yu3daMvDoMvmo6fGfQFzkTcAMCb9cfum1cTp51W000XeNlvkv',
        ]);
    }

    #[Route('/subscribe', name: 'app_subscribe', methods: ['POST'])]
    public function subscribe(Request $request, EntityManagerInterface $em, UserInterface $user): Response
    {
        if ($user->isSubscribed()) {
            return $this->json(['success' => false, 'message' => 'Vous êtes déjà abonné']);
        }

        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $paymentMethod = $data['payment_method'];

        Stripe::setApiKey('sk_test_51N1m7HB8NXWXURY9cnq0pT8MVnBwb4s4mUaW3D1z3Pel7jwYQjfsezlh3SXVdfo3Ic4fVej3bdC2P8euKhUpHq8R00FrPw8ACR');

        try {
            $customer = Customer::create([
                'email' => $email,
                'payment_method' => $paymentMethod,
                'invoice_settings' => [
                    'default_payment_method' => $paymentMethod,
                ],
            ]);

            $subscription = Subscription::create([
                'customer' => $customer->id,
                'items' => [[
                    'price' => 'price_1Pab22B8NXWXURY9vaFDcA0n',
                ]],
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            $user->setSubscribed(true);
            $em->persist($user);
            $em->flush();

            return $this->json(['success' => true, 'subscription' => $subscription]);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }   
    }
}
