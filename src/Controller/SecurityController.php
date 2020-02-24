<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $user = new User();
        $form = $this->createForm(LoginUserType::class, $user);
        return $this->render('Security/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'form' => $form->createView()
        ]);
    }

}
