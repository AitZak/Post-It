<?php


namespace App\Controller;

use App\Entity\Content;
use App\Manager\ApprovalManager;
use App\Manager\ContentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dashboard")
 */
class CommunicatorController extends AbstractController
{
    /**
     * @Route("/", name="dashboard_index", methods={"GET","POST"})
     */
    public function dashboard(ApprovalManager $approvalManager, ContentManager $contentManager): Response
    {
        if ($this->getUser() === null) {
            return $this->render('main/error_connection.html.twig');
        }

        if ($this->getUser()->getRoles() !== ['ROLE_ADMIN'] && $this->getUser()->getRoles() !== ['ROLE_COMM']) {
            return $this->render('main/error_role.html.twig');
        }

        $contents = $contentManager->getLastAcceptedContents();
        $approvalStats = $approvalManager->getApprovalStatistics();
        $contentStats = $contentManager->getContentStatistics();
        return $this->render('communicator/dashboard.html.twig', [
            'contents' => $contents,
            'contentStats' => $contentStats,
            'approvalStats' => $approvalStats,
            ]);
    }
}