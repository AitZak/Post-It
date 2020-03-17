<?php

namespace App\Controller;

use App\Entity\Approval;
use App\Form\ApprovalType;
use App\Manager\ApprovalManager;
use App\Manager\ContentManager;
use App\Repository\ApprovalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/approval")
 */
class ApprovalController extends AbstractController
{
    /**
     * @Route("/", name="approval_index", methods={"GET"})
     */
    public function index(ApprovalRepository $approvalRepository): Response
    {
        return $this->render('approval/index.html.twig', [
            'approvals' => $approvalRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="approval_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em, ContentManager $contentManager, ApprovalManager $approvalManager): Response
    {
        $date = new \DateTime();
        $content = $contentManager->getContentById(intval($request->get('content_id')));
        if (!$content->getApprovalDate()){
            $content->setApprovalDate($date);
        }
        if ($content->getStatut() === 2) {
            return new Response(
                "Ce contenu a été refusé par un autre reviewer, veuillez passer par un communicant si vous souhaitez que ce contenu soit publié."
            );
        }
        $content->setStatut(intval($request->get('status')));
        $approval = new Approval();

        $approval->setUser($this->getUser());
        $approval->setContent($content);

        $review = $approvalManager->alreadyReviewed($approval);

        if (!$review){
            $approval->setStatus(intval($request->get('status')));
            $em->persist($approval);
            $em->flush();

            return $this->redirectToRoute('content_show', [
                'id'=>$content->getId()
            ]);
        }
        $review->setStatus(intval($request->get('status')));
        $em->flush();

        return $this->redirectToRoute('content_show', [
            'id'=>$content->getId()
        ]);
    }

    /**
     * @Route("/{user_id}", name="approval_show", methods={"GET"})
     */
    public function show(ApprovalManager $approvalManager): Response
    {
        $user = $this->getUser();
        $acceptedReviews = $approvalManager->getAccpetedReviewsByUser($user);
        $rejectedReviews = $approvalManager->getRejectedReviewsByUser($user);


        return $this->render('approval/show.html.twig', [
            'acceptedReviews' => $acceptedReviews,
            'rejectedReviews' => $rejectedReviews,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="approval_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Approval $approval): Response
    {
        $form = $this->createForm(ApprovalType::class, $approval);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('approval_index');
        }

        return $this->render('approval/edit.html.twig', [
            'approval' => $approval,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="approval_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Approval $approval): Response
    {
        if ($this->isCsrfTokenValid('delete'.$approval->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($approval);
            $entityManager->flush();
        }

        return $this->redirectToRoute('approval_index');
    }
}
