<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Content;
use App\Entity\Modification;
use App\Form\CommentType;
use App\Form\ContentType;
use App\Manager\ApprovalManager;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/content")
 */
class ContentController extends AbstractController
{
    /**
     * @Route("/", name="content_index", methods={"GET"})
     */
    public function index(): Response
    {
        $contents = $this->getDoctrine()
            ->getRepository(Content::class)
            ->findAll();

        return $this->render('content/index.html.twig', [
            'contents' => $contents,
        ]);
    }

    /**
     * @Route("/new", name="content_new", methods={"GET","POST"})
     */
    public function new(Request $request,FileUploader $fileUploader): Response
    {
        $content = new Content();
        $form = $this->createForm(ContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('file')->getData();
            if ($file) {
                $fileName = $fileUploader->upload($file);
                $content->setFile($fileName);
            }

            $date = new \DateTime();
            $content->setSubmitDate($date);

            $content->setUserSubmit($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($content);
            $entityManager->flush();

            return $this->redirectToRoute('content_index');
        }

        return $this->render('content/new.html.twig', [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="content_show", methods={"GET","POST"})
     */
    public function show(Content $content, Request $request,EntityManagerInterface $entityManager): Response
    {
        $comments = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findBy(
                [
                    'content' => $content,
                ]
            );

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setContent($content);
            $comment->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('content_show', ['id'=>$content->getId()]);
        }

        return $this->render('content/show.html.twig', [
            'comment_form' => $commentForm->createView(),
            'content' => $content,
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="content_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EntityManagerInterface $em, Content $content, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(ContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('file')->getData();
            if ($file) {
                $fileName = $fileUploader->upload($file);
                $content->setFile($fileName);
            }

            $this->getDoctrine()->getManager()->flush();

            $modification = new Modification();
            $date = new \DateTime;
            $date = $date->format('Y-m-d H:i:s');
            $modification->setDateModification($date);
            $modification->setUser($this->getUser());
            $modification->setContent($content);
            $modification->setDescription($content->getDescription());
            $em->persist($modification);
            $em->flush();

            return $this->redirectToRoute('content_index');
        }

        return $this->render('content/edit.html.twig', [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="content_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Content $content): Response
    {
        if ($this->isCsrfTokenValid('delete'.$content->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($content);
            $entityManager->flush();
        }

        return $this->redirectToRoute('content_index');
    }
}
