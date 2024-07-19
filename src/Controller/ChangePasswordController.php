<?php

namespace App\Controller;

use App\Form\UpdatePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/profil')]
class ChangePasswordController extends AbstractController
{
    #[Route('/change-password', name: 'app_change_password', methods: ['GET', 'POST'])]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UpdatePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();

            // Verify that the old password matches the current password
            if ($passwordHasher->isPasswordValid($user, $oldPassword)) {
                // Encode the new password
                $encodedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($encodedPassword);

                $entityManager->flush();

                $this->addFlash('success', 'Votre mot de passe a été changé avec succès.');
                return $this->redirectToRoute('app_dashboard');
            } else {
                $this->addFlash('error', 'L\'ancien mot de passe est incorrect.');
            }
        }

        return $this->render('profil/change_password.html.twig', [
            'changePasswordForm' => $form->createView(),
        ]);
    }
}
