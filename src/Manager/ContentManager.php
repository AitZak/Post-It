<?php

namespace App\Manager;

use App\Entity\Content;
use App\Entity\User;
use App\Repository\ContentRepository;
use Doctrine\ORM\EntityManagerInterface;

class ContentManager
{
    private $contentRepository;

    public function __construct(ContentRepository $contentRepository)
    {
        $this->contentRepository = $contentRepository;
    }

    public function getContentById(int $contentId): ?Content
    {
        return $this->contentRepository->findOneBy(['id' => $contentId]);
    }

    public function getLastAcceptedContents()
    {
        return $this->contentRepository->findBy(['statut' => 1]);
    }

    public function getContentStatistics()
    {
        $infos = [];

        $contents = $this->contentRepository->findAll();
        $reviewedContents = $this->contentRepository->findBy(['statut' => [1,2]]);
        $publishedContents = $this->contentRepository->findBy(['statut' => 3]);
        $infos['nbContents'] = count($contents);
        $infos['nbReviewedContents'] = count($reviewedContents);
        $infos['nbPublishedContents'] = count($publishedContents);

        return $infos;
    }

    public function getSubmissionsByUser(User $user)
    {
        $infos = [];

        $submissions = $this->contentRepository->findBy(['userSubmit' => $user]);
        $unreviewedSubmissions = $this->contentRepository->findBy(['userSubmit' => $user, 'statut' => 0]);
        $acceptedSubmissions = $this->contentRepository->findBy(['userSubmit' => $user, 'statut' => 1]);
        $refusedSubmissions = $this->contentRepository->findBy(['userSubmit' => $user, 'statut' => 2]);
        $publishedSubmissions = $this->contentRepository->findBy(['userSubmit' => $user, 'statut' => 3]);

        $infos['submissions'] = $submissions;
        $infos['unreviewedSubmissions'] = $unreviewedSubmissions;
        $infos['acceptedSubmissions'] = $acceptedSubmissions;
        $infos['refusedSubmissions'] = $refusedSubmissions;
        $infos['publishedSubmissions'] = $publishedSubmissions;

        return $infos;
    }

    public function getPublicatedSubmissionsByUser(User $user)
    {
        $infos = [];

        $publications = $this->contentRepository->findBy(['userPublish' => $user, 'statut' => 3]);

        $infos['nbPublications'] = count($publications);
        $infos['publications'] = $publications;

        return $infos;
    }

    public function prepareContentToDeletion(
        Content $content,
        CommentManager $commentManager,
        ApprovalManager $approvalManager,
        PublicationManager $publicationManager,
        ModificationManager $modificationManager
    )
    {
        $comments = $commentManager->getCommentByContent($content);
        $approvals = $approvalManager->getAllApprovalsByContent($content);
        $publications = $publicationManager->getPublicationsByContent($content);
        $modifications = $modificationManager->getModificationsByContent($content);

        return [
            'comments' => $comments,
            'approvals' => $approvals['approvals'],
            'publications' => $publications,
            'modifications' => $modifications,
        ];
    }

}