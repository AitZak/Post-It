<?php


namespace App\Manager;


use App\Entity\Content;
use App\Entity\User;
use App\Repository\PublicationRepository;

class PublicationManager
{
    private $publicationRepository;

    public function __construct(PublicationRepository $publicationRepository)
    {
        $this->publicationRepository = $publicationRepository;
    }

    public function getPublicationsByContent(Content $content)
    {
        return $this->publicationRepository->findBy(['content' => $content]);
    }

    public function getPublicationsByUser(User $user)
    {
        return $this->publicationRepository->findBy(['user' => $user]);
    }

}