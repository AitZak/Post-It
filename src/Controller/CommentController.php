<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/", name="comment_index", methods={"GET"})
     */
    public function index(): Response
    {
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        if ($this->getUser()->getRoles() !== ['ROLE_ADMIN']) {
            return $this->render('main/error_role.html.twig');
        }

        $comments = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findAll();

        return $this->render('comment/index.html.twig', [
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/new", name="comment_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('comment_index');
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comment_show", methods={"GET"})
     */
    public function show(Comment $comment): Response
    {
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="comment_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Comment $comment): Response
    {
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        if ($comment->getUser() !== $this->getUser() && $this->getUser()->getRoles() !== ['ROLE_ADMIN']) {
            return $this->render('main/error_role.html.twig');
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('content_show', ['id'=>$comment->getContent()->getId()]);
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'comment_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comment_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        if ($comment->getUser() !== $this->getUser() && $this->getUser()->getRoles() !== ['ROLE_ADMIN']) {
            return $this->render('main/error_role.html.twig');
        }

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('content_show', ['id'=>$comment->getContent()->getId()]);
    }
}
