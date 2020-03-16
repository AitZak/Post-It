<?php


namespace App\Manager;


use App\Entity\Approval;
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

}