<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * login method, redirect to the target page
     * @Route("login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
           return $this->redirectToRoute('home');
        }

        $lastUsername = $authenticationUtils->getLastUsername();
        $lastError = $authenticationUtils->getLastAuthenticationError();
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $lastError
        ]);
    }
}
