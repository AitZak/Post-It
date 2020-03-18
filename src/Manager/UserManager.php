<?php


namespace App\Manager;


use App\Entity\User;
use App\Repository\UserRepository;

class UserManager
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function prepareUserToDeletion(
        User $user,
        ContentManager $contentManager,
        CommentManager $commentManager,
        ApprovalManager $approvalManager,
        PublicationManager $publicationManager,
        ModificationManager $modificationManager
    )
    {
        $contents = $contentManager->getSubmissionsByUser($user);
        $comments = $commentManager->getCommentByUser($user);
        $approvals = $approvalManager->getApprovalsByUser($user);
        $publications = $publicationManager->getPublicationsByUser($user);
        $modifications = $modificationManager->getModificationsByUser($user);

        return [
            'contents' => $contents['submissions'],
            'comments' => $comments,
            'approvals' => $approvals['approvals'],
            'publications' => $publications,
            'modifications' => $modifications,
        ];
    }

}