<?php

namespace App\Controller;

use App\Entity\Content;
use App\Entity\Publication;
use App\Entity\User;
use App\Form\RegisterUserType;
use App\Form\UserType;
use App\Manager\ApprovalManager;
use App\Manager\ContentManager;
use App\Repository\PublicationRepository;
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
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        if ($this->getUser()->getRoles() !== ['ROLE_ADMIN']) {
            return $this->render('main/error_role.html.twig');
        }

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
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        if ($this->getUser()->getRoles() !== ['ROLE_ADMIN']) {
            return $this->render('main/error_role.html.twig');
        }

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
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        if ($user !== $this->getUser() && $this->getUser()->getRoles() !== ['ROLE_ADMIN']) {
            return $this->render('main/error_role.html.twig');
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        if ($user !== $this->getUser() && $this->getUser()->getRoles() !== ['ROLE_ADMIN']) {
            return $this->render('main/error_role.html.twig');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        if ($this->getUser()->getRoles() !== ['ROLE_ADMIN']) {
            return $this->render('main/error_role.html.twig');
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/{id}/submissions", name="user_submissions", methods={"GET"})
     */
    public function mySubmissions(User $user, ContentManager $contentManager): Response
    {
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        $submissions = $contentManager->getSubmissionsByUser($user);
        return $this->render('user/submissions.html.twig', [
            'user' => $user,
            'submissions' => $submissions,
        ]);
    }

    /**
     * @Route("/{id}/reviews", name="user_reviews", methods={"GET"})
     */
    public function myReviews(User $user, ApprovalManager $approvalManager): Response
    {
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        if ($this->getUser()->getRoles() !== ['ROLE_ADMIN'] && $this->getUser()->getRoles() !== ['ROLE_COMM'] && $this->getUser()->getRoles() !== ['ROLE_REVIEWER']) {
            return $this->render('main/error_role.html.twig');
        }

        $reviews = $approvalManager->getApprovalsByUser($user);
        return $this->render('user/reviews.html.twig', [
            'user' => $user,
            'reviews' => $reviews,
        ]);
    }

    /**
     * @Route("/{id}/publications", name="user_publications", methods={"GET"})
     */
    public function myPublications(User $user, ContentManager $contentManager): Response
    {
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        if ($this->getUser()->getRoles() !== ['ROLE_ADMIN'] && $this->getUser()->getRoles() !== ['ROLE_COMM']) {
            return $this->render('main/error_role.html.twig');
        }

        $publications = $contentManager->getPublicatedSubmissionsByUser($user);
        return $this->render('user/publications.html.twig', [
            'user' => $user,
            'publicationsInfos' => $publications,
        ]);
    }
}
