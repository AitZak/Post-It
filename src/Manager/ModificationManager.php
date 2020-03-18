<?php


namespace App\Manager;


use App\Entity\Content;
use App\Entity\User;
use App\Repository\ModificationRepository;

class ModificationManager
{
    private $modificationRepository;

    public function __construct(ModificationRepository $modificationRepository)
    {
        $this->modificationRepository = $modificationRepository;
    }

    public function getModificationsByContent(Content $content)
    {
        return $this->modificationRepository->findBy(['content' => $content]);
    }

    public function getModificationsByUser(User $user)
    {
        return $this->modificationRepository->findBy(['user' => $user]);
    }
}