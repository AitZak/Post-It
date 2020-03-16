<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\SocialNetworkRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(): Response
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder, SocialNetworkRepository $socialNetworkRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        $socialNetworks = $socialNetworkRepository -> findAll();
        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'socialNetworks'=> $socialNetworks
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }


    /**
     * @Route("/userSocialNetwork_add", name="userSocialNetwork_add", methods={"POST"})
     */
    public function userSocialNetwork_add(Request $request, UserRepository $userRepository,
                                          SocialNetworkRepository $socialNetworkRepository): Response
    {

        $user = $userRepository -> findOneBy(["id" => $request -> get('idUser')]);
        $socialNetwork = $socialNetworkRepository -> findOneBy(["id" => $request -> get('SN-select')]);
        $socialNetwork -> addUser($user);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('user_edit', ['id' => $request -> get('idUser')] );
    }

    /**
     * @Route("/userSocialNetwork_delete", name="userSocialNetwork_delete", methods={"POST"})
     */
    public function userSocialNetwork_delete(Request $request, UserRepository $userRepository,
                                          SocialNetworkRepository $socialNetworkRepository): Response
    {

        $user = $userRepository -> findOneBy(["id" => $request -> get('idUser')]);
        $socialNetwork = $socialNetworkRepository -> findOneBy(["id" => $request -> get('SN-select')]);
        $socialNetwork -> removeUser($user);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('user_edit', ['id' => $request -> get('idUser')] );
    }
}
