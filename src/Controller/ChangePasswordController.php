<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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

        $form = $this->createForm(ChangePasswordFormType::class);
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

                $this->addFlash('success', 'Your password has been changed successfully.');

                return $this->redirectToRoute('app_profil');
            } else {
                $this->addFlash('error', 'The current password is incorrect.');
            }
        }

        return $this->render('profil/change_password.html.twig', [
            'changePasswordForm' => $form->createView(),
        ]);
    }
}
