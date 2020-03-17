<?php

namespace App\Manager;

use App\Entity\Content;
use App\Entity\User;
use App\Repository\ContentRepository;

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

        $submissions = $this->contentRepository->findBy(['userSubmit' => $user, 'statut' => 0]);
        $acceptedSubmissions = $this->contentRepository->findBy(['userSubmit' => $user, 'statut' => 1]);
        $refusedSubmissions = $this->contentRepository->findBy(['userSubmit' => $user, 'statut' => 2]);
        $publishedSubmissions = $this->contentRepository->findBy(['userSubmit' => $user, 'statut' => 3]);

        $infos['unreviewedSubmissions'] = $submissions;
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

}