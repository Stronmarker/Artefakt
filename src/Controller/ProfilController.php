<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfilController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/profil', name: 'app_profil')]
    public function profil(Request $request, UserInterface $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Synchronisation avec Stripe
                Stripe::setApiKey($_ENV['STRIPE_API_KEY']);
                
                $stripeCustomerId = $user->getStripeCustomerId();

                if ($stripeCustomerId) {
                    // Mise à jour du client existant sur Stripe
                    $customer = Customer::update($stripeCustomerId, [
                        'email' => $user->getEmail(),
                        'name' => $user->getFirstname() . ' ' . $user->getLastname(),
                        'address' => [
                            'line1' => $user->getAddress(),
                            'city' => $user->getCity(),
                            'state' =>$user->getState(),
                            'postal_code' =>$user->getPostalCode(),
                            'country' =>$user->getCountry(),
                        ]
                    ]);
                } else {
                    // Création d'un nouveau client sur Stripe
                    $customer = Customer::create([
                        'email' => $user->getEmail(),
                        'name' => $user->getFirstname() . ' ' . $user->getLastname(),
                        'address' => [
                            'line1' => $user->getAddress(),
                            'city' => $user->getCity(),
                            'state' => $user->getState(),
                            'postal_code' => $user->getPostalCode(), 
                            'country' => $user->getCountry(),
                        ],
                    ]);
                    $user->setStripeCustomerId($customer->id);
                }

                // Persiste l'utilisateur dans la base de données
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->addFlash('success', 'Vos informations ont été mises à jour.');

                return $this->redirectToRoute('app_profil');
            } catch (ApiErrorException $e) {
                $this->addFlash('error', 'Erreur lors de la mise à jour sur Stripe: ' . $e->getMessage());
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue: ' . $e->getMessage());
            }
        }

        return $this->render('profil/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
