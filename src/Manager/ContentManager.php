<?php

namespace App\Manager;

use App\Entity\Content;
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

}