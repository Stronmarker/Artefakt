<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Webhook;
use App\Entity\Invoice;
use App\Entity\User;

class StripeWebhookController extends AbstractController
{
    #[Route('/stripe/webhook', name: 'stripe_webhook')]
    public function handleWebhook(Request $request, EntityManagerInterface $em): Response
    {
        $payload = @file_get_contents('php://input');
        $sigHeader = $request->headers->get('stripe-signature');
        $endpointSecret = $_ENV['STRIPE_WEBHOOK_SECRET'];

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return new Response('Invalid payload', Response::HTTP_BAD_REQUEST);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return new Response('Invalid signature', Response::HTTP_BAD_REQUEST);
        }

        // Handle the event
        if ($event->type === 'invoice.payment_succeeded') {
            $stripeInvoice = $event->data->object;

            // Retrieve the user based on Stripe customer ID
            $user = $em->getRepository(User::class)->findOneBy(['stripeCustomerId' => $stripeInvoice->customer]);

            if ($user) {
                $invoice = new Invoice();
                $invoice->setStripeInvoiceId($stripeInvoice->id);
                $invoice->setCreatedAt(new \DateTime('@' . $stripeInvoice->created));
                $invoice->setAmount($stripeInvoice->amount_paid / 100); // Convert from cents to dollars/euros
                $user->addInvoice($invoice);

                $em->persist($invoice);
                $em->flush();
            }
        }

        return new Response('Webhook handled', Response::HTTP_OK);
    }
}
