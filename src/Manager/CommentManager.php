<?php


namespace App\Manager;


use App\Entity\Content;
use App\Entity\User;
use App\Repository\CommentRepository;

class CommentManager
{
    private $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function getCommentByContent(Content $content)
    {
        return $this->commentRepository->findBy(['content' => $content]);
    }

    public function getCommentByUser(User $user)
    {
        return $this->commentRepository->findBy(['user' => $user]);
    }
}