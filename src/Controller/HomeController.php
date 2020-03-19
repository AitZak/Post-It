<?php

namespace App\Controller;

use App\Repository\ContentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ContentRepository $contentRepository)
    {
        $contentPublish = $contentRepository -> findBy(["statut" => 3]);
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'contents' => $contentPublish,
        ]);
    }

}
