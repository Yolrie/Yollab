<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    // Step 8 : profil public
    #[Route('/{username}', name: 'app_profile_show')]
    public function show(string $username, UserRepository $repo): Response
    {
        $user = $repo->findOneBy(['username' => $username]);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        return $this->render('profile/show.html.twig', ['profile' => $user]);
    }

    // Profil de l'utilisateur connecté
    #[Route('', name: 'app_profile_me')]
    public function me(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        return $this->redirectToRoute('app_profile_show', [
            'username' => $user->getUsername(),
        ]);
    }
}
