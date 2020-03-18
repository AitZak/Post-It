<?php

namespace App\Controller;

use App\Entity\Approval;
use App\Entity\Publication;
use App\Manager\ApprovalManager;
use App\Manager\ContentManager;
use App\Repository\ApprovalRepository;
use App\Repository\ContentRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Api\TumblrApi;

/**
 * @Route("/publication")
 */
class PublicationController extends AbstractController
{

    /**
     * @Route("/publish", name="publish")
     */
    public function publishContent(Request $request, ContentRepository $contentRepository, TumblrApi $tumblrApi,
                                   EntityManagerInterface $entityManager)
    {

        $content = $contentRepository ->findOneBy(["id" => $request->get("contentId")]);

        $user = $this->getUser();
        $socialsNetworks = $user -> getSocialNetwork();

        $contentValues['title'] = $content -> getTitle();
        if($content ->getFile()){
            $contentValues['file'] = $content -> getFile();
            $contentValues['type'] = $content -> getTypeFile();
        }
        if($content -> getDescription()){
            $contentValues['description'] = $content -> getDescription();
        }

        foreach($socialsNetworks as $socialsNetwork){
            if($socialsNetwork -> getName() == 'tumblr'){
                $consumer_key = $socialsNetwork -> getClientId();
                $consumer_secret = $socialsNetwork -> getClientSecret();
                $token = $socialsNetwork -> getToken();
                $token_secret = $socialsNetwork -> getTokenSecret();
                $data = $tumblrApi->createData($contentValues);
                $this -> postContent($data, $consumer_key, $consumer_secret, $token, $token_secret);
                $publication = new Publication();
                $publication->setUser($user);
                $publication->setContent($content);
                $publication->setSocialNetwork($socialsNetwork);
                $entityManager->persist($publication);
                $entityManager->flush();
            }
        }

        $date = new \DateTime();
        $content->setPublicationDate($date);
        $content->setStatut(3);
        $content->setUserPublish($user);
        $entityManager ->persist($content);
        $entityManager ->flush();
        return $this->redirectToRoute('content_index');
    }

    public function postContent($data, $consumer_key, $consumer_secret, $token, $token_secret){
        $stack = HandlerStack::create();
        $middleware = new Oauth1([
            'consumer_key' => $consumer_key,
            'consumer_secret' => $consumer_secret,
            'token' => $token,
            'token_secret' => $token_secret]);
        $stack->push($middleware);

        $client = new Client([
            'base_uri' => 'https://api.tumblr.com/v2/',
            'handler' => $stack,
            'auth' => 'oauth',
            'headers' => [ 'Content-Type' => 'application/json' ]
        ]);


        $res = $client->post('blog/androgynouskingtale/post',['body' => json_encode($data)]);
//        $end = json_decode($res->getBody(), true);
//        return $this->render('test.html.twig', [
//            'test' => $end,]);
    }
}
