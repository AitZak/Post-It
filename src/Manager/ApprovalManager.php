<?php


namespace App\Manager;


use App\Entity\Approval;
use App\Entity\Content;
use App\Entity\User;
use App\Repository\ApprovalRepository;

class ApprovalManager
{
    private $approvalRepository;

    public function __construct(ApprovalRepository $approvalRepository)
    {
        $this->approvalRepository = $approvalRepository;
    }

    public function alreadyReviewed(Approval $approval): ?Approval
    {
     return $this->approvalRepository->findOneBy(
         [
             'user' => $approval->getUser(),
             'content' => $approval->getContent(),
             ]
     );
    }

    public function getAccpetedReviewsByUser(User $user)
    {

        return $this->approvalRepository->findBy(
            [
                'user' => $user,
                'status' => 1,
            ]
        );
    }

    public function getRejectedReviewsByUser(User $user)
    {

        return $this->approvalRepository->findBy(
            [
                'user' => $user,
                'status' => 2,
            ]
        );
    }

    public function getApprovalStatistics()
    {
        $infos = [];

        $approvals = $this->approvalRepository->findAll();
        $approvalsAccepted = $this->approvalRepository->findBy(['status' => 1]);
        $approvalsRejected= $this->approvalRepository->findBy(['status' => 2]);
        $infos['nbApprovals'] = count($approvals);
        $infos['nbAcceptedApprovals'] = count($approvalsAccepted);
        $infos['nbRejectedApprovals'] = count($approvalsRejected);

         return $infos;
    }

    public function getAllApprovalsByContent(Content $content)
    {
        $infos = [];

        $approvals = $this->approvalRepository->findBy(['content' => $content]);
        $acceptedApprovals = $this->approvalRepository->findBy(['content' => $content, 'status' => 1]);
        $rejectedApprovals = $this->approvalRepository->findBy(['content' => $content, 'status' => 2]);
        $infos['approvals'] = $approvals;
        $infos['acceptedApprovals'] = $acceptedApprovals;
        $infos['rejectedApprovals'] = $rejectedApprovals;
        $infos['nbApprovals'] = count($approvals);
        $infos['nbAcceptedApprovals'] = count($acceptedApprovals);
        $infos['nbRejectedApprovals'] = count($rejectedApprovals);

        return $infos;
    }

    public function getCurrentUserApprovalByContent(Content $content, User $user): ?Approval
    {
        return $this->approvalRepository->findOneBy(['content' => $content, 'user' => $user]);
    }

    public function getApprovalsByUser($user)
    {
        $infos = [];
        $approvals = $this->approvalRepository->findAll();
        $approvalsAccepted = $this->approvalRepository->findBy(['status' => 1]);
        $approvalsRejected= $this->approvalRepository->findBy(['status' => 2]);
        $infos['nbReviews'] = count($approvals);
        $infos['acceptedReviews'] = $approvalsAccepted;
        $infos['rejectedReviews'] = $approvalsRejected;

        return $infos;
    }

}